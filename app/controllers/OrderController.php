<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Coupon;

class OrderController extends Controller
{
    public function checkout(): void
    {
        $cart = (new Cart())->current(Auth::id(), session_id());
        $items = (new Cart())->items((int) $cart['id']);
        if (!$items) {
            Session::flash('info', 'Giỏ hàng trống.');
            $this->redirect('/products');
        }
        $total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
        $this->view('checkout/index', [
            'title' => 'Thanh toán',
            'items' => $items,
            'total' => $total,
            'user'  => Auth::user(),
        ]);
    }

    public function place(): void
    {
        Csrf::verify();

        $cartModel = new Cart();
        $cart = $cartModel->current(Auth::id(), session_id());
        $items = $cartModel->items((int) $cart['id']);
        if (!$items) {
            Session::flash('error', 'Giỏ hàng trống.');
            $this->redirect('/cart');
        }

        $method = $this->input('payment_method');
        $method = in_array($method, ['vnpay', 'sepay'], true) ? $method : 'cod';
        $street   = trim($this->input('street'));
        $ward     = trim($this->input('ward_name'));
        $province = trim($this->input('province_name'));
        $addrParts = array_filter([$street, $ward, $province]);
        $address = implode(', ', $addrParts);

        $info = [
            'user_id'          => Auth::id(),
            'payment_method'   => $method,
            'shipping_address' => $address,
            'phone'            => $this->input('phone'),
            'customer_name'    => $this->input('name'),
            'note'             => $this->input('note'),
        ];

        if ($info['shipping_address'] === '' || $info['phone'] === '' || $info['customer_name'] === '') {
            Session::flash('error', 'Vui lòng điền đầy đủ thông tin giao hàng.');
            $this->redirect('/checkout');
        }

        // Áp dụng coupon nếu có
        $couponCode = strtoupper(trim($this->input('coupon_code')));
        $discount   = 0;
        $couponId   = null;
        if ($couponCode !== '') {
            $couponModel = new Coupon();
            $coupon = $couponModel->findByCode($couponCode);
            if ($coupon) {
                $rawTotal = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
                $discount = $couponModel->calcDiscount($coupon, (int) $rawTotal);
                $couponId = $coupon['id'];
            }
        }
        $info['discount'] = $discount;

        try {
            $orderModel = new Order();
            $orderId = $orderModel->placeOrder($info, $items);
            if ($couponId) (new Coupon())->incrementUsed($couponId);
            $cartModel->clear((int) $cart['id']);
            if (!Auth::id()) {
                $_SESSION['guest_order_id'] = $orderId;
            }
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            $this->redirect('/cart');
            return;
        }

        if ($method === 'sepay') {
            // Chuyển sang trang QR chuyển khoản SePay
            $this->redirect('/payment/sepay/' . $orderId);
        }

        Session::flash('success', 'Đặt hàng thành công! Mã đơn #' . $orderId);
        $this->redirect('/order/success/' . $orderId);
    }

    public function success(string $id): void
    {
        $orderId = (int) $id;
        $orderModel = new Order();

        if (Auth::id()) {
            $order = $orderModel->findForUser($orderId, Auth::id());
        } else {
            $guestId = $_SESSION['guest_order_id'] ?? null;
            $order = ($guestId === $orderId) ? $orderModel->find($orderId) : null;
        }

        if (!$order) { (new HomeController())->notFound(); return; }

        // SePay chưa thanh toán → về trang QR
        if ($order['payment_method'] === 'sepay' && $order['payment_status'] !== 'paid') {
            $this->redirect('/payment/sepay/' . $orderId);
        }

        $this->view('checkout/success', [
            'title' => 'Đặt hàng thành công',
            'order' => $order,
            'items' => $orderModel->items($orderId),
        ]);
    }
}
