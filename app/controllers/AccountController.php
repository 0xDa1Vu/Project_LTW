<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\Order;
use App\Models\User;

class AccountController extends Controller
{
    public function index(): void
    {
        Auth::require();
        $this->view('account/index', [
            'title' => 'Tài khoản',
            'user'  => Auth::user(),
        ]);
    }

    public function orders(): void
    {
        Auth::require();
        $this->view('account/orders', [
            'title'  => 'Đơn hàng của tôi',
            'orders' => (new Order())->forUser(Auth::id()),
        ]);
    }

    public function orderDetail(string $id): void
    {
        Auth::require();
        $order = (new Order())->findForUser((int) $id, Auth::id());
        if (!$order) { (new HomeController())->notFound(); return; }
        $this->view('account/order_detail', [
            'title' => 'Đơn #' . $id,
            'order' => $order,
            'items' => (new Order())->items((int) $id),
        ]);
    }

    public function updateProfile(): void
    {
        Auth::require();
        Csrf::verify();
        (new User())->updateProfile(Auth::id(), [
            'name'    => $this->input('name'),
            'phone'   => $this->input('phone'),
            'address' => $this->input('address'),
        ]);
        Session::set('user_name', $this->input('name'));
        Session::flash('success', 'Đã cập nhật thông tin.');
        $this->redirect('/account');
    }
}
