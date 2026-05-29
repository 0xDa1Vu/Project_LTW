<?php use App\Core\Csrf; ?>
<table class="data-table">
    <thead><tr><th>ID</th><th>Sản phẩm</th><th>Khách</th><th>Điểm</th><th>Nội dung</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($reviews as $r): ?>
        <tr>
            <td><?= (int) $r['id'] ?></td>
            <td><?= e($r['product_name']) ?></td>
            <td><?= e($r['user_name']) ?></td>
            <td><?= str_repeat('★', (int) $r['rating']) ?></td>
            <td><?= e($r['comment']) ?></td>
            <td>
                <form action="/admin/reviews/delete/<?= (int) $r['id'] ?>" method="post" onsubmit="return confirm('Xoá đánh giá?')">
                    <?= Csrf::field() ?>
                    <button class="link-btn danger">Xoá</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
