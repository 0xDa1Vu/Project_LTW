<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index(): void
    {
        $product = new Product();
        $this->view('home/index', [
            'title'       => 'Trang chủ',
            'featured'    => $product->featured(8),
            'bestSellers' => $product->bestSellers(4),
            'categories'  => (new Category())->allOrdered(),
        ]);
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->view('errors/404', ['title' => 'Không tìm thấy']);
    }
}
