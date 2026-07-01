<?php use App\Core\Csrf; ?>
<div class="admin-panel">
    <p><a href="/admin/stylings/create" class="btn btn-dark">+ Thêm styling</a></p>
    <table class="data-table">
        <thead><tr><th>Ảnh</th><th>Tiêu đề</th><th>Thứ tự</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($stylings as $s): ?>
            <tr>
                <td>
                    <?php if (!empty($covers[$s['id']])): ?>
                        <img src="<?= e($covers[$s['id']]) ?>" alt="" style="width:60px;height:80px;object-fit:cover">
                    <?php else: ?>
                        <span style="color:var(--gray-3)">— chưa có ảnh —</span>
                    <?php endif; ?>
                </td>
                <td><?= e($s['title']) ?></td>
                <td><?= (int) $s['sort_order'] ?></td>
                <td>
                    <a href="/admin/stylings/edit/<?= (int) $s['id'] ?>" class="link-btn">Sửa</a>
                    <form action="/admin/stylings/delete/<?= (int) $s['id'] ?>" method="post" onsubmit="return confirm('Xoá?')" style="display:inline">
                        <?= Csrf::field() ?>
                        <button class="link-btn danger">Xoá</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
