<section class="container section">
    <h1 class="section-title">Đơn hàng của tôi</h1>
    <?php if (empty($orders)): ?>
        <p class="empty-note">Bạn chưa có đơn hàng nào.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr><th>Mã</th><th>Ngày</th><th>Tổng</th><th>Thanh toán</th><th>Trạng thái</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td>#<?= (int) $o['id'] ?></td>
                    <td><?= e(date('d/m/Y', strtotime($o['created_at']))) ?></td>
                    <td><?= vnd($o['total']) ?></td>
                    <td><?= e($o['payment_status']) ?></td>
                    <td><span class="status-badge status-<?= e($o['status']) ?>"><?= e($o['status']) ?></span></td>
                    <td><a href="/account/order/<?= (int) $o['id'] ?>">Chi tiết</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
