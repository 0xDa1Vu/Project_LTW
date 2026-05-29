<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(): void
    {
        Auth::requireRole('admin');
        $this->view('admin/orders/index', [
            'title'  => 'Quản lý đơn hàng',
            'orders' => (new Order())->allWithUser(),
        ], 'admin');
    }

    public function show(string $id): void
    {
        Auth::requireRole('admin');
        $order = (new Order())->find((int) $id);
        if (!$order) { $this->redirect('/admin/orders'); }
        $this->view('admin/orders/show', [
            'title' => 'Đơn #' . $id,
            'order' => $order,
            'items' => (new Order())->items((int) $id),
        ], 'admin');
    }

    public function updateStatus(string $id): void
    {
        Auth::requireRole('admin');
        Csrf::verify();
        $allowed = ['pending','confirmed','shipping','completed','cancelled'];
        $status = $this->input('status');
        if (in_array($status, $allowed, true)) {
            (new Order())->setStatus((int) $id, $status);
            Session::flash('success', 'Đã cập nhật trạng thái đơn.');
        }
        $this->redirect('/admin/orders/' . $id);
    }
}
