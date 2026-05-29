<table class="data-table">
    <thead>
        <tr><th>Mã</th><th>Khách</th><th>Tổng</th><th>TT</th><th>Trạng thái</th><th>Ngày</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($orders as $o): ?>
        <tr>
            <td>#<?= (int) $o['id'] ?></td>
            <td><?= e($o['customer_name']) ?><br><small><?= e($o['email'] ?? '') ?></small></td>
            <td><?= vnd($o['total']) ?></td>
            <td><?= e($o['payment_status']) ?></td>
            <td><span class="status-badge status-<?= e($o['status']) ?>"><?= e($o['status']) ?></span></td>
            <td><?= e(date('d/m/Y', strtotime($o['created_at']))) ?></td>
            <td><a href="/admin/orders/<?= (int) $o['id'] ?>">Xem</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
