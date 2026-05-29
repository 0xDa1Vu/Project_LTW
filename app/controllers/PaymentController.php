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
}
