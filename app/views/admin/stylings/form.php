<?php
use App\Core\Csrf;
$isEdit = $styling !== null;
$action = $isEdit ? '/admin/stylings/update/' . (int) $styling['id'] : '/admin/stylings/store';
?>
<div class="admin-form">
<form id="main-form" action="<?= $action ?>" method="post" enctype="multipart/form-data">
    <?= Csrf::field() ?>
    <label>Tiêu đề
        <input type="text" name="title" value="<?= e($styling['title'] ?? '') ?>" required>
    </label>
    <label>Thứ tự
        <input type="number" name="sort_order" value="<?= (int) ($styling['sort_order'] ?? 0) ?>">
    </label>
    <label>Thông tin model (tuỳ chọn)
        <textarea name="model_info" rows="3" placeholder="Model: Height 1m75, Weight 55kg&#10;Wearing: Size 3"><?= e($styling['model_info'] ?? '') ?></textarea>
    </label>

    <h3>Hình ảnh</h3>
</form>

<!-- Quản lý ảnh hiện có — NGOÀI form chính để tránh nested form -->
<?php if (!empty($images)): ?>
<div class="image-manage-row" id="sortableStylingImages">
    <?php foreach ($images as $img): ?>
    <div class="image-manage-item <?= $img['is_cover'] ? 'is-primary' : '' ?>" data-id="<?= (int) $img['id'] ?>">
        <img src="<?= e($img['image_url']) ?>" alt="">
        <?php if ($img['is_cover']): ?>
            <span class="img-badge">Đại diện</span>
        <?php endif; ?>
        <div class="img-actions">
            <?php if (!$img['is_cover']): ?>
            <form method="post" action="/admin/stylings/image/cover/<?= (int) $img['id'] ?>">
                <?= Csrf::field() ?>
                <button type="submit" class="img-btn img-btn-primary" title="Đặt làm ảnh đại diện">⭐</button>
            </form>
            <?php endif; ?>
            <form method="post" action="/admin/stylings/image/delete/<?= (int) $img['id'] ?>"
                  onsubmit="return confirm('Xoá ảnh này?')">
                <?= Csrf::field() ?>
                <button type="submit" class="img-btn img-btn-delete" title="Xoá ảnh">✕</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<p class="hint">Kéo thả để sắp xếp thứ tự hiển thị.</p>
<?php endif; ?>

<!-- Choose Files + nút Cập nhật — dùng form="main-form" để liên kết vào form chính -->
<label class="upload-label">
    <span>+ Thêm ảnh mới</span>
    <input type="file" name="images[]" multiple accept="image/*" form="main-form">
</label>
<div class="form-footer">
    <button type="submit" form="main-form" class="btn btn-dark"><?= $isEdit ? 'Cập nhật' : 'Thêm mới' ?></button>
    <a href="/admin/stylings" class="btn btn-outline">Huỷ</a>
</div>
</div><!-- /.admin-form -->

<?php if (!empty($images)): ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
Sortable.create(document.getElementById('sortableStylingImages'), {
    animation: 150,
    ghostClass: 'sortable-ghost',
    onEnd() {
        const ids = [...document.querySelectorAll('#sortableStylingImages [data-id]')]
            .map(el => el.dataset.id);
        fetch('/admin/stylings/image/reorder', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ ids })
        });
    }
});
</script>
<?php endif; ?>
