<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Variant;
use App\Models\ProductImage;
use App\Models\Review;

class ProductController extends Controller
{
    public function index(): void
    {
        $product = new Product();
        $filters = [
            'category_id' => $_GET['category'] ?? null,
            'q'           => $_GET['q'] ?? null,
            'min_price'   => $_GET['min_price'] ?? '',
            'max_price'   => $_GET['max_price'] ?? '',
            'size'        => $_GET['size'] ?? null,
            'color'       => $_GET['color'] ?? null,
            'sort'        => $_GET['sort'] ?? '',
            'page'        => $_GET['page'] ?? 1,
            'per_page'    => 12,
        ];
        $result = $product->browse($filters);

        $this->view('product/index', [
            'title'      => 'Sản phẩm',
            'result'     => $result,
            'filters'    => $filters,
            'categories' => (new Category())->allOrdered(),
            'sizes'      => $product->distinctSizes(),
            'colors'     => $product->distinctColors(),
        ]);
    }

    public function show(string $slug): void
    {
        $productModel = new Product();
        $product = $productModel->findBySlug($slug);
        if (!$product) {
            (new HomeController())->notFound();
            return;
        }
        $full = $productModel->withCategory((int) $product['id']);

        $this->view('product/show', [
            'title'    => $product['name'],
            'product'  => $full,
            'images'   => (new ProductImage())->forProduct((int) $product['id']),
            'variants' => (new Variant())->forProduct((int) $product['id']),
            'reviews'  => (new Review())->forProduct((int) $product['id']),
            'summary'  => (new Review())->summary((int) $product['id']),
        ]);
    }
}
