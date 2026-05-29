<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\Cart;
use App\Models\Order;

class OrderController extends Controller
{
    public function checkout(): void
    {
        Auth::require();
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
        Auth::require();
        Csrf::verify();

        $cartModel = new Cart();
        $cart = $cartModel->current(Auth::id(), session_id());
        $items = $cartModel->items((int) $cart['id']);
        if (!$items) {
            Session::flash('error', 'Giỏ hàng trống.');
            $this->redirect('/cart');
        }

        $method = $this->input('payment_method') === 'vnpay' ? 'vnpay' : 'cod';
        $info = [
            'user_id'          => Auth::id(),
            'payment_method'   => $method,
            'shipping_address' => $this->input('address'),
            'phone'            => $this->input('phone'),
            'customer_name'    => $this->input('name'),
            'note'             => $this->input('note'),
        ];

        if ($info['shipping_address'] === '' || $info['phone'] === '' || $info['customer_name'] === '') {
            Session::flash('error', 'Vui lòng điền đầy đủ thông tin giao hàng.');
            $this->redirect('/checkout');
        }

        try {
            $orderModel = new Order();
            $orderId = $orderModel->placeOrder($info, $items);
            $cartModel->clear((int) $cart['id']);
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            $this->redirect('/cart');
            return;
        }

        if ($method === 'vnpay') {
            // Chuyển sang luồng VNPay (PaymentController dựng URL)
            $this->redirect('/payment/vnpay/create?order_id=' . $orderId);
        }

        Session::flash('success', 'Đặt hàng thành công! Mã đơn #' . $orderId);
        $this->redirect('/order/success/' . $orderId);
    }

    public function success(string $id): void
    {
        Auth::require();
        $order = (new Order())->findForUser((int) $id, Auth::id());
        if (!$order) { (new HomeController())->notFound(); return; }
        $this->view('checkout/success', [
            'title' => 'Đặt hàng thành công',
            'order' => $order,
            'items' => (new Order())->items((int) $id),
        ]);
    }
}
