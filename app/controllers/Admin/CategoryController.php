<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Core\Str;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(): void
    {
        Auth::requireRole('admin');
        $this->view('admin/categories/index', [
            'title'      => 'Quản lý danh mục',
            'categories' => (new Category())->allOrdered(),
        ], 'admin');
    }

    public function store(): void
    {
        Auth::requireRole('admin');
        Csrf::verify();
        $name = $this->input('name');
        if ($name === '') {
            Session::flash('error', 'Tên danh mục bắt buộc.');
            $this->redirect('/admin/categories');
        }
        (new Category())->create([
            'name'      => $name,
            'slug'      => $this->input('slug') ?: Str::slug($name),
            'parent_id' => $this->input('parent_id') ?: null,
        ]);
        Session::flash('success', 'Đã thêm danh mục.');
        $this->redirect('/admin/categories');
    }

    public function update(string $id): void
    {
        Auth::requireRole('admin');
        Csrf::verify();
        $name = $this->input('name');
        (new Category())->modify((int) $id, [
            'name'      => $name,
            'slug'      => $this->input('slug') ?: Str::slug($name),
            'parent_id' => $this->input('parent_id') ?: null,
        ]);
        Session::flash('success', 'Đã cập nhật danh mục.');
        $this->redirect('/admin/categories');
    }

    public function destroy(string $id): void
    {
        Auth::requireRole('admin');
        Csrf::verify();
        (new Category())->delete((int) $id);
        Session::flash('success', 'Đã xoá danh mục.');
        $this->redirect('/admin/categories');
    }
}
