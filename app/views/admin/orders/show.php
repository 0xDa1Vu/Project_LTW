<?php use App\Core\Csrf; $statuses = ['pending','confirmed','shipping','completed','cancelled']; ?>
<div class="admin-split">
    <div class="admin-panel">
        <h3>Đơn #<?= (int) $order['id'] ?></h3>
        <p>Người nhận: <?= e($order['customer_name']) ?> · <?= e($order['phone']) ?></p>
        <p>Địa chỉ: <?= e($order['shipping_address']) ?></p>
        <p>Ghi chú: <?= e($order['note'] ?? '—') ?></p>
        <p>Thanh toán: <?= $order['payment_method'] === 'vnpay' ? 'VNPay' : 'COD' ?> (<?= e($order['payment_status']) ?>)</p>

        <div class="order-items">
            <?php foreach ($items as $it): ?>
                <div class="co-row">
                    <span><?= e($it['product_name']) ?> <small><?= e($it['variant_label']) ?></small> ×<?= (int) $it['quantity'] ?></span>
                    <span><?= vnd($it['price'] * $it['quantity']) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="co-total"><span>Tổng</span><strong><?= vnd($order['total']) ?></strong></div>
        </div>
    </div>
    <div class="admin-panel">
        <h3>Cập nhật trạng thái</h3>
        <form action="/admin/orders/status/<?= (int) $order['id'] ?>" method="post" class="admin-form">
            <?= Csrf::field() ?>
            <select name="status">
                <?php foreach ($statuses as $s): ?>
                    <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-dark">Lưu</button>
        </form>
        <a href="/admin/orders" class="btn btn-outline">← Danh sách</a>
    </div>
</div>
