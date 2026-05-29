<?php use App\Core\Csrf; ?>
<div class="admin-toolbar">
    <a href="/admin/products/create" class="btn btn-dark">+ Thêm sản phẩm</a>
</div>
<table class="data-table">
    <thead>
        <tr><th>ID</th><th>Tên</th><th>Giá</th><th>Sale</th><th>Trạng thái</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($products as $p): ?>
        <tr>
            <td><?= (int) $p['id'] ?></td>
            <td><?= e($p['name']) ?></td>
            <td><?= vnd($p['price']) ?></td>
            <td><?= $p['sale_price'] ? vnd($p['sale_price']) : '—' ?></td>
            <td><?= e($p['status']) ?></td>
            <td class="row-actions">
                <a href="/admin/products/edit/<?= (int) $p['id'] ?>">Sửa</a>
                <form action="/admin/products/delete/<?= (int) $p['id'] ?>" method="post" onsubmit="return confirm('Xoá sản phẩm này?')">
                    <?= Csrf::field() ?>
                    <button class="link-btn danger">Xoá</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
