<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index(): void
    {
        Auth::requireRole('admin');
        $this->view('admin/reviews/index', [
            'title'   => 'Quản lý đánh giá',
            'reviews' => (new Review())->allWithRefs(),
        ], 'admin');
    }

    public function destroy(string $id): void
    {
        Auth::requireRole('admin');
        Csrf::verify();
        (new Review())->delete((int) $id);
        Session::flash('success', 'Đã xoá đánh giá.');
        $this->redirect('/admin/reviews');
    }
}
