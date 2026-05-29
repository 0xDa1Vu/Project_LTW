<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\Order;
use App\Models\Payment;

/**
 * Tích hợp VNPay sandbox. Tham khảo tài liệu chính thức VNPay.
 * Lưu ý: cần điền VNP_TMN_CODE và VNP_HASH_SECRET trong .env.
 */
class PaymentController extends Controller
{
    public function vnpayCreate(): void
    {
        Auth::require();
        $orderId = (int) ($_GET['order_id'] ?? 0);
        $order = (new Order())->findForUser($orderId, Auth::id());
        if (!$order) { (new HomeController())->notFound(); return; }

        $tmnCode = cfg('vnpay.tmn_code');
        $secret  = cfg('vnpay.hash_secret');
        if (!$tmnCode || !$secret) {
            Session::flash('error', 'Chưa cấu hình VNPay (VNP_TMN_CODE / VNP_HASH_SECRET). Đơn đã được tạo, vui lòng thanh toán COD.');
            $this->redirect('/order/success/' . $orderId);
        }

        $vnpUrl = cfg('vnpay.url');
        $returnUrl = cfg('vnpay.return_url');
        $txnRef = $orderId . '_' . time();

        $data = [
            'vnp_Version'    => '2.1.0',
            'vnp_Command'    => 'pay',
            'vnp_TmnCode'    => $tmnCode,
            'vnp_Amount'     => (int) round($order['total'] * 100), // nhân 100 theo chuẩn VNPay
            'vnp_CurrCode'   => 'VND',
            'vnp_TxnRef'     => $txnRef,
            'vnp_OrderInfo'  => 'Thanh toan don hang #' . $orderId,
            'vnp_OrderType'  => 'other',
            'vnp_Locale'     => 'vn',
            'vnp_ReturnUrl'  => $returnUrl,
            'vnp_IpAddr'     => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'vnp_CreateDate' => date('YmdHis'),
        ];

        ksort($data);
        $hashData = http_build_query($data, '', '&', PHP_QUERY_RFC3986);
        $secureHash = hash_hmac('sha512', $hashData, $secret);

        (new Payment())->record($orderId, 'vnpay', (float) $order['total'], $txnRef, 'pending');

        $this->redirect($vnpUrl . '?' . $hashData . '&vnp_SecureHash=' . $secureHash);
    }

    public function vnpayReturn(): void
    {
        $secret = cfg('vnpay.hash_secret');
        $input = $_GET;
        $receivedHash = $input['vnp_SecureHash'] ?? '';
        unset($input['vnp_SecureHash'], $input['vnp_SecureHashType']);

        ksort($input);
        $hashData = http_build_query($input, '', '&', PHP_QUERY_RFC3986);
        $calcHash = hash_hmac('sha512', $hashData, $secret);

        $txnRef = $input['vnp_TxnRef'] ?? '';
        $orderId = (int) explode('_', $txnRef)[0];
        $orderModel = new Order();

        $valid = hash_equals($calcHash, $receivedHash);
        $success = $valid && ($input['vnp_ResponseCode'] ?? '') === '00';

        (new Payment())->record(
            $orderId, 'vnpay',
            ((int) ($input['vnp_Amount'] ?? 0)) / 100,
            $txnRef,
            $success ? 'paid' : 'failed',
            $input
        );

        if ($success) {
            $orderModel->setPayment($orderId, 'paid', 'confirmed');
            Session::flash('success', 'Thanh toán VNPay thành công! Mã đơn #' . $orderId);
        } else {
            $orderModel->setPayment($orderId, 'failed');
            Session::flash('error', $valid
                ? 'Thanh toán thất bại hoặc bị huỷ. Đơn vẫn được giữ, bạn có thể thanh toán lại.'
                : 'Chữ ký VNPay không hợp lệ.');
        }
        $this->redirect('/order/success/' . $orderId);
    }

    // =================================================================
    // SePay — chuyển khoản QR (VietQR) + webhook đối soát tự động
    // =================================================================

    /**
     * Nội dung chuyển khoản chuẩn cho 1 đơn, vd "DH12".
     * SePay đọc nội dung này để khớp đơn hàng.
     */
    public static function sepayContent(int $orderId): string
    {
        return cfg('sepay.prefix') . $orderId;
    }

    /** Trang hiển thị mã QR VietQR + hướng dẫn, chờ thanh toán. */
    public function sepayShow(string $id): void
    {
        Auth::require();
        $orderId = (int) $id;
        $order = (new Order())->findForUser($orderId, Auth::id());
        if (!$order) { (new HomeController())->notFound(); return; }

        // Đã thanh toán rồi thì về trang thành công luôn.
        if ($order['payment_status'] === 'paid') {
            $this->redirect('/order/success/' . $orderId);
        }

        $amount  = (int) round((float) $order['total']);
        $content = self::sepayContent($orderId);
        $acc     = cfg('sepay.account_number');
        $bank    = cfg('sepay.bank');

        // Ảnh QR động VietQR (miễn phí, không cần thư viện).
        $qrUrl = sprintf(
            'https://img.vietqr.io/image/%s-%s-compact2.png?amount=%d&addInfo=%s&accountName=%s',
            rawurlencode($bank),
            rawurlencode($acc),
            $amount,
            rawurlencode($content),
            rawurlencode(cfg('sepay.account_name'))
        );

        $this->view('checkout/sepay', [
            'title'       => 'Chuyển khoản QR — Đơn #' . $orderId,
            'order'       => $order,
            'amount'      => $amount,
            'content'     => $content,
            'qrUrl'       => $qrUrl,
            'account'     => $acc,
            'bank'        => $bank,
            'accountName' => cfg('sepay.account_name'),
        ]);
    }

    /**
     * Endpoint nhận webhook từ SePay khi có giao dịch tiền vào.
     * SePay gửi JSON kèm header "Authorization: Apikey <api_key>".
     * Tài liệu: https://docs.sepay.vn/tich-hop-webhooks.html
     */
    public function sepayWebhook(): void
    {
        $apiKey = cfg('sepay.api_key');
        // Apache/mod_php không luôn đẩy Authorization vào $_SERVER -> ưu tiên getallheaders().
        $auth = '';
        if (function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $auth = $v; break; }
            }
        }
        if ($auth === '') {
            $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        }
        // Chấp nhận "Apikey xxx" hoặc "Bearer xxx".
        $token = preg_replace('/^(Apikey|Bearer)\s+/i', '', trim($auth));
        if ($apiKey === '' || !hash_equals($apiKey, $token)) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $payload = json_decode(file_get_contents('php://input'), true) ?: [];

        // Chỉ xử lý tiền vào (transferType = "in").
        $type = $payload['transferType'] ?? '';
        if ($type !== '' && $type !== 'in') {
            $this->json(['success' => true, 'message' => 'Ignored (not incoming)']);
        }

        $amountIn = (float) ($payload['transferAmount'] ?? 0);
        // Nội dung CK có thể nằm ở content/description; gộp lại để dò mã đơn.
        $rawText  = trim(($payload['content'] ?? '') . ' ' . ($payload['description'] ?? ''));

        $orderId = $this->parseOrderId($rawText);
        if ($orderId <= 0) {
            // Vẫn ghi nhận để đối soát thủ công, nhưng không gắn được đơn.
            $this->json(['success' => true, 'message' => 'No order matched']);
        }

        $orderModel = new Order();
        $order = $orderModel->find($orderId);
        $paymentModel = new Payment();

        if (!$order || $order['payment_method'] !== 'sepay') {
            $this->json(['success' => true, 'message' => 'Order not applicable']);
        }

        $expected = (float) $order['total'];
        $paid = $amountIn + 0.0001 >= $expected; // cho phép sai số làm tròn

        $paymentModel->record(
            $orderId, 'sepay', $amountIn,
            (string) ($payload['referenceCode'] ?? $payload['id'] ?? self::sepayContent($orderId)),
            $paid ? 'paid' : 'failed',
            $payload
        );

        if ($paid && $order['payment_status'] !== 'paid') {
            $orderModel->setPayment($orderId, 'paid', 'confirmed');
        }

        $this->json(['success' => true]);
    }

    /**
     * Nút "Tôi đã chuyển khoản / Kiểm tra" trên trang QR.
     * Trả JSON trạng thái thanh toán hiện tại của đơn (do webhook cập nhật).
     */
    public function sepayCheck(string $id): void
    {
        Auth::require();
        $orderId = (int) $id;
        $order = (new Order())->findForUser($orderId, Auth::id());
        if (!$order) {
            $this->json(['paid' => false, 'message' => 'Không tìm thấy đơn.'], 404);
        }
        $paid = $order['payment_status'] === 'paid';
        $this->json([
            'paid'    => $paid,
            'message' => $paid
                ? 'Đã nhận thanh toán! Đơn hàng được xác nhận.'
                : 'Chưa nhận được chuyển khoản. Vui lòng đợi vài giây sau khi chuyển và thử lại.',
        ]);
    }

    /** Dò mã đơn từ nội dung CK, vd "DH12" -> 12. Khớp tiền tố cấu hình. */
    private function parseOrderId(string $text): int
    {
        $prefix = preg_quote(cfg('sepay.prefix'), '/');
        if (preg_match('/' . $prefix . '0*(\d+)/i', $text, $m)) {
            return (int) $m[1];
        }
        return 0;
    }
}
