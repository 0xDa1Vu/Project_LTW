<?php use App\Core\Csrf; ?>
<div class="admin-split">
    <div class="admin-panel">
        <h3>Thêm danh mục</h3>
        <form action="/admin/categories/store" method="post" class="admin-form">
            <?= Csrf::field() ?>
            <label>Tên<input type="text" name="name" required></label>
            <label>Slug (tuỳ chọn)<input type="text" name="slug"></label>
            <label>Danh mục cha
                <select name="parent_id">
                    <option value="">— Không —</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int) $c['id'] ?>"><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button class="btn btn-dark">Thêm</button>
        </form>
    </div>
    <div class="admin-panel">
        <?php
        // Build lookup id → name để hiện tên danh mục cha
        $catMap = [];
        foreach ($categories as $c) { $catMap[$c['id']] = $c['name']; }
        ?>
        <table class="data-table">
            <thead><tr><th>ID</th><th>Tên</th><th>Slug</th><th>Danh mục cha</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($categories as $c): ?>
                <tr>
                    <td><?= (int) $c['id'] ?></td>
                    <td><?= $c['parent_id'] ? '&nbsp;&nbsp;↳ ' : '' ?><?= e($c['name']) ?></td>
                    <td><?= e($c['slug']) ?></td>
                    <td><?= $c['parent_id'] ? e($catMap[$c['parent_id']] ?? '—') : '<span style="color:var(--gray-3)">—</span>' ?></td>
                    <td>
                        <form action="/admin/categories/delete/<?= (int) $c['id'] ?>" method="post" onsubmit="return confirm('Xoá?')">
                            <?= Csrf::field() ?>
                            <button class="link-btn danger">Xoá</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
