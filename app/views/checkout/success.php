<section class="container section narrow">
    <div class="success-box">
        <div class="success-icon">✓</div>
        <h1>Đặt hàng thành công!</h1>
        <p>Mã đơn hàng: <strong>#<?= (int) $order['id'] ?></strong></p>
        <p>Trạng thái: <span class="status-badge status-<?= e($order['status']) ?>"><?= e($order['status']) ?></span></p>
        <?php $pmLabels = ['vnpay' => 'VNPay', 'sepay' => 'Chuyển khoản QR (SePay)', 'cod' => 'COD']; ?>
        <p>Thanh toán: <?= $pmLabels[$order['payment_method']] ?? 'COD' ?>
           (<?= e($order['payment_status']) ?>)</p>

        <?php if ($order['payment_method'] === 'sepay' && $order['payment_status'] !== 'paid'): ?>
            <p><a href="/payment/sepay/<?= (int) $order['id'] ?>" class="btn btn-outline">Thanh toán lại bằng QR</a></p>
        <?php endif; ?>

        <div class="order-items">
            <?php foreach ($items as $it): ?>
                <div class="co-row">
                    <span><?= e($it['product_name']) ?> <small><?= e($it['variant_label']) ?></small> ×<?= (int) $it['quantity'] ?></span>
                    <span><?= vnd($it['price'] * $it['quantity']) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="co-total">
                <span>Tổng</span><strong><?= vnd($order['total']) ?></strong>
            </div>
        </div>

        <a href="/products" class="btn btn-dark">Tiếp tục mua sắm</a>
        <a href="/account/orders" class="btn btn-outline">Xem đơn hàng</a>
    </div>
</section>
