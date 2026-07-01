<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Styling;
use App\Models\StylingImage;

class HomeController extends Controller
{
    public function index(): void
    {
        $product = new Product();
        $cat = new Category();
        $stylings = (new Styling())->allOrdered();
        $this->view('home/index', [
            'title'       => 'Trang chủ',
            'featured'    => $product->featured(12),
            'bestSellers' => $product->bestSellers(12),
            'categories'  => $cat->allOrdered(),
            'categoryGroups' => $cat->allGrouped(),
            'stylings'      => $stylings,
            'stylingCovers' => (new StylingImage())->coversFor(array_column($stylings, 'id')),
        ]);
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->view('errors/404', ['title' => 'Không tìm thấy']);
    }
}
