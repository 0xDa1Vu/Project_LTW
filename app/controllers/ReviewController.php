<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\Review;
use App\Models\Product;

class ReviewController extends Controller
{
    public function store(): void
    {
        Auth::require();
        Csrf::verify();

        $productId = (int) $this->input('product_id');
        $rating = (int) $this->input('rating');
        $comment = $this->input('comment', '');

        $product = (new Product())->find($productId);
        if (!$product || $rating < 1 || $rating > 5) {
            Session::flash('error', 'Dữ liệu đánh giá không hợp lệ.');
            $this->redirect('/');
        }

        (new Review())->upsert($productId, Auth::id(), $rating, $comment);
        Session::flash('success', 'Cảm ơn bạn đã đánh giá!');
        $this->redirect('/product/' . $product['slug']);
    }
}
