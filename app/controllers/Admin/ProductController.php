<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Core\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\Variant;
use App\Models\ProductImage;

class ProductController extends Controller
{
    public function index(): void
    {
        Auth::requireRole('admin');
        $this->view('admin/products/index', [
            'title'    => 'Quản lý sản phẩm',
            'products' => (new Product())->all(),
        ], 'admin');
    }

    public function create(): void
    {
        Auth::requireRole('admin');
        $this->view('admin/products/form', [
            'title'      => 'Thêm sản phẩm',
            'product'    => null,
            'variants'   => [],
            'images'     => [],
            'categories' => (new Category())->allOrdered(),
        ], 'admin');
    }

    public function store(): void
    {
        Auth::requireRole('admin');
        Csrf::verify();
        $id = (new Product())->create($this->payload());
        $this->saveVariants($id);
        $this->saveImages($id);
        Session::flash('success', 'Đã thêm sản phẩm.');
        $this->redirect('/admin/products');
    }

    public function edit(string $id): void
    {
        Auth::requireRole('admin');
        $product = (new Product())->find((int) $id);
        if (!$product) { $this->redirect('/admin/products'); }
        $this->view('admin/products/form', [
            'title'      => 'Sửa sản phẩm',
            'product'    => $product,
            'variants'   => (new Variant())->forProduct((int) $id),
            'images'     => (new ProductImage())->forProduct((int) $id),
            'categories' => (new Category())->allOrdered(),
        ], 'admin');
    }

    public function update(string $id): void
    {
        Auth::requireRole('admin');
        Csrf::verify();
        (new Product())->modify((int) $id, $this->payload());
        // Thay toàn bộ variant (đơn giản cho đồ án)
        (new Variant())->deleteForProduct((int) $id);
        $this->saveVariants((int) $id);
        $this->saveImages((int) $id); // thêm ảnh mới nếu có upload
        Session::flash('success', 'Đã cập nhật sản phẩm.');
        $this->redirect('/admin/products');
    }

    public function destroy(string $id): void
    {
        Auth::requireRole('admin');
        Csrf::verify();
        (new Product())->delete((int) $id);
        Session::flash('success', 'Đã xoá sản phẩm.');
        $this->redirect('/admin/products');
    }

    // ---- helpers ----

    private function payload(): array
    {
        $name = $this->input('name');
        return [
            'category_id' => $this->input('category_id') ?: null,
            'name'        => $name,
            'slug'        => $this->input('slug') ?: Str::slug($name) . '-' . substr(uniqid(), -4),
            'description' => $this->input('description'),
            'price'       => (float) $this->input('price'),
            'sale_price'  => $this->input('sale_price'),
            'brand'       => $this->input('brand'),
            'status'      => $this->input('status') === 'hidden' ? 'hidden' : 'active',
        ];
    }

    private function saveVariants(int $productId): void
    {
        $sizes  = $_POST['v_size']  ?? [];
        $colors = $_POST['v_color'] ?? [];
        $stocks = $_POST['v_stock'] ?? [];
        $variant = new Variant();
        foreach ($sizes as $i => $size) {
            if (trim($size) === '' || trim($colors[$i] ?? '') === '') {
                continue;
            }
            $variant->add($productId, [
                'size'  => trim($size),
                'color' => trim($colors[$i]),
                'stock' => (int) ($stocks[$i] ?? 0),
                'sku'   => $productId . '-' . Str::slug($size . '-' . $colors[$i]),
            ]);
        }
    }

    private function saveImages(int $productId): void
    {
        if (empty($_FILES['images']['name'][0])) {
            return;
        }
        $uploadDir = dirname(__DIR__, 3) . '/public/uploads/';
        if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }

        $imageModel = new ProductImage();
        $existing = $imageModel->forProduct($productId);
        $hasPrimary = !empty($existing);
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) { continue; }
            if ($_FILES['images']['size'][$i] > 5 * 1024 * 1024) { continue; } // 5MB
            $mime = mime_content_type($tmp);
            if (!in_array($mime, $allowed, true)) { continue; }
            $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
            $fname = bin2hex(random_bytes(8)) . '.' . preg_replace('/[^a-z0-9]/i', '', $ext);
            if (move_uploaded_file($tmp, $uploadDir . $fname)) {
                $imageModel->add($productId, '/uploads/' . $fname, !$hasPrimary);
                $hasPrimary = true;
            }
        }
    }
}
