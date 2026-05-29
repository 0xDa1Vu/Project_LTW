<section class="container section narrow">
    <h1 class="section-title">Đơn hàng #<?= (int) $order['id'] ?></h1>
    <div class="order-meta">
        <p>Ngày đặt: <?= e(date('d/m/Y H:i', strtotime($order['created_at']))) ?></p>
        <p>Người nhận: <?= e($order['customer_name']) ?> · <?= e($order['phone']) ?></p>
        <p>Địa chỉ: <?= e($order['shipping_address']) ?></p>
        <p>Thanh toán: <?= $order['payment_method'] === 'vnpay' ? 'VNPay' : 'COD' ?> (<?= e($order['payment_status']) ?>)</p>
        <p>Trạng thái: <span class="status-badge status-<?= e($order['status']) ?>"><?= e($order['status']) ?></span></p>
    </div>
    <div class="order-items">
        <?php foreach ($items as $it): ?>
            <div class="co-row">
                <span><?= e($it['product_name']) ?> <small><?= e($it['variant_label']) ?></small> ×<?= (int) $it['quantity'] ?></span>
                <span><?= vnd($it['price'] * $it['quantity']) ?></span>
            </div>
        <?php endforeach; ?>
        <div class="co-total"><span>Tổng</span><strong><?= vnd($order['total']) ?></strong></div>
    </div>
</section>
