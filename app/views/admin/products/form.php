<?php
use App\Core\Csrf;
$isEdit = $product !== null;
$action = $isEdit ? '/admin/products/update/' . (int) $product['id'] : '/admin/products/store';
?>
<div class="admin-form">
<form id="main-form" action="<?= $action ?>" method="post" enctype="multipart/form-data">
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
        <label>Hiển thị ở New Arrival (trang chủ)
            <select name="is_featured">
                <option value="1" <?= !empty($product['is_featured']) ? 'selected' : '' ?>>Hiển thị</option>
                <option value="0" <?= empty($product['is_featured']) ? 'selected' : '' ?>>Không hiển thị</option>
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

    <!-- Hình ảnh -->
    <h3>Hình ảnh</h3>
</form>

<!-- Quản lý ảnh hiện có — NGOÀI form chính để tránh nested form -->
<?php if (!empty($images)): ?>
<div class="image-manage-row" id="sortableImages">
    <?php foreach ($images as $img): ?>
    <div class="image-manage-item <?= $img['is_primary'] ? 'is-primary' : '' ?>" data-id="<?= (int)$img['id'] ?>">
        <img src="<?= e($img['image_url']) ?>" alt="">
        <?php if ($img['is_primary']): ?>
            <span class="img-badge">Chính</span>
        <?php endif; ?>
        <div class="img-actions">
            <?php if (!$img['is_primary']): ?>
            <form method="post" action="/admin/products/image/primary/<?= (int) $img['id'] ?>">
                <?= Csrf::field() ?>
                <button type="submit" class="img-btn img-btn-primary" title="Đặt làm ảnh chính">⭐</button>
            </form>
            <?php endif; ?>
            <form method="post" action="/admin/products/image/delete/<?= (int) $img['id'] ?>"
                  onsubmit="return confirm('Xoá ảnh này?')">
                <?= Csrf::field() ?>
                <button type="submit" class="img-btn img-btn-delete" title="Xoá ảnh">✕</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Choose Files + nút Cập nhật — dùng form="main-form" để liên kết vào form chính -->
<label class="upload-label">
    <span>+ Thêm ảnh mới</span>
    <input type="file" name="images[]" multiple accept="image/*" form="main-form">
</label>
<div class="form-footer">
    <button type="submit" form="main-form" class="btn btn-dark"><?= $isEdit ? 'Cập nhật' : 'Thêm mới' ?></button>
    <a href="/admin/products" class="btn btn-outline">Huỷ</a>
</div>
</div><!-- /.admin-form -->

<?php if (!empty($images)): ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
Sortable.create(document.getElementById('sortableImages'), {
    animation: 150,
    ghostClass: 'sortable-ghost',
    onEnd() {
        const ids = [...document.querySelectorAll('#sortableImages [data-id]')]
            .map(el => el.dataset.id);
        fetch('/admin/products/image/reorder', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ ids })
        });
    }
});
</script>
<?php endif; ?>
