<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\User;

class UserController extends Controller
{
    public function index(): void
    {
        Auth::requireRole('admin');
        $this->view('admin/users/index', [
            'title' => 'Quản lý người dùng',
            'users' => (new User())->all(),
        ], 'admin');
    }

    public function updateRole(string $id): void
    {
        Auth::requireRole('admin');
        Csrf::verify();
        $role = $this->input('role') === 'admin' ? 'admin' : 'customer';
        (new User())->setRole((int) $id, $role);
        Session::flash('success', 'Đã cập nhật quyền.');
        $this->redirect('/admin/users');
    }

    public function destroy(string $id): void
    {
        Auth::requireRole('admin');
        Csrf::verify();
        if ((int) $id === Auth::id()) {
            Session::flash('error', 'Không thể tự xoá tài khoản đang đăng nhập.');
            $this->redirect('/admin/users');
        }
        (new User())->delete((int) $id);
        Session::flash('success', 'Đã xoá người dùng.');
        $this->redirect('/admin/users');
    }
}
