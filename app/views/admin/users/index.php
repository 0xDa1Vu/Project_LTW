<?php use App\Core\Csrf; ?>
<table class="data-table">
    <thead><tr><th>ID</th><th>Tên</th><th>Email</th><th>Quyền</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= (int) $u['id'] ?></td>
            <td><?= e($u['name']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td>
                <form action="/admin/users/role/<?= (int) $u['id'] ?>" method="post" class="inline-form">
                    <?= Csrf::field() ?>
                    <select name="role" onchange="this.form.submit()">
                        <option value="customer" <?= $u['role']==='customer'?'selected':'' ?>>customer</option>
                        <option value="admin" <?= $u['role']==='admin'?'selected':'' ?>>admin</option>
                    </select>
                </form>
            </td>
            <td>
                <form action="/admin/users/delete/<?= (int) $u['id'] ?>" method="post" onsubmit="return confirm('Xoá người dùng?')">
                    <?= Csrf::field() ?>
                    <button class="link-btn danger">Xoá</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
