<?php
use App\Core\Csrf;
$isEdit = $product !== null;
$action = $isEdit ? '/admin/products/update/' . (int) $product['id'] : '/admin/products/store';
?>
<form action="<?= $action ?>" method="post" enctype="multipart/form-data" class="admin-form">
    <?= Csrf::field() ?>
    <div class="form-grid">
        <label>Tên sản phẩm
            <input type="text" name="name" value="<?= e($product['name'] ?? '') ?>" required>
        </label>
        <label>Slug (để trống = tự tạo)
            <input type="text" name="slug" value="<?= e($product['slug'] ?? '') ?>">
        </label>
        <label>Danh mục
            <select name="category_id">
                <option value="">— Không —</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= (int) $c['id'] ?>" <?= ($product['category_id'] ?? null) == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Thương hiệu
            <input type="text" name="brand" value="<?= e($product['brand'] ?? '') ?>">
        </label>
        <label>Giá
            <input type="number" name="price" step="1000" value="<?= e($product['price'] ?? '') ?>" required>
        </label>
        <label>Giá sale (tuỳ chọn)
            <input type="number" name="sale_price" step="1000" value="<?= e($product['sale_price'] ?? '') ?>">
        </label>
        <label>Trạng thái
            <select name="status">
                <option value="active" <?= ($product['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Hiển thị</option>
                <option value="hidden" <?= ($product['status'] ?? '') === 'hidden' ? 'selected' : '' ?>>Ẩn</option>
            </select>
        </label>
    </div>
    <label>Mô tả
        <textarea name="description" rows="4"><?= e($product['description'] ?? '') ?></textarea>
    </label>

    <h3>Biến thể (size / màu / tồn kho)</h3>
    <p class="hint"><?= $isEdit ? 'Lưu ý: lưu sẽ thay toàn bộ biến thể bên dưới.' : '' ?></p>
    <table class="variant-table" id="variantTable">
        <thead><tr><th>Size</th><th>Màu</th><th>Tồn kho</th><th></th></tr></thead>
        <tbody>
        <?php
        $rows = !empty($variants) ? $variants : [['size'=>'','color'=>'','stock'=>'']];
        foreach ($rows as $v): ?>
            <tr>
                <td><input type="text" name="v_size[]" value="<?= e($v['size']) ?>"></td>
                <td><input type="text" name="v_color[]" value="<?= e($v['color']) ?>"></td>
                <td><input type="number" name="v_stock[]" value="<?= e($v['stock']) ?>"></td>
                <td><button type="button" class="link-btn danger" onclick="this.closest('tr').remove()">✕</button></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <button type="button" class="btn btn-outline" id="addVariantRow">+ Thêm biến thể</button>

    <h3>Hình ảnh</h3>
    <?php if (!empty($images)): ?>
        <div class="image-preview-row">
            <?php foreach ($images as $img): ?>
                <img src="<?= e($img['image_url']) ?>" alt="">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <input type="file" name="images[]" multiple accept="image/*">

    <div class="form-footer">
        <button type="submit" class="btn btn-dark"><?= $isEdit ? 'Cập nhật' : 'Thêm mới' ?></button>
        <a href="/admin/products" class="btn btn-outline">Huỷ</a>
    </div>
</form>
