<?php use App\Core\Csrf; ?>
<section class="container section">
    <h1 class="section-title">Thanh toán</h1>
    <form action="/checkout" method="post" class="checkout-layout">
        <?= Csrf::field() ?>
        <div class="checkout-form">
            <h3>Thông tin giao hàng</h3>
            <label>Họ tên người nhận
                <input type="text" name="name" value="<?= e($user['name']) ?>" required>
            </label>
            <label>Số điện thoại
                <input type="text" name="phone" value="<?= e($user['phone'] ?? '') ?>" required>
            </label>
            <label>Địa chỉ giao hàng
                <textarea name="address" rows="3" required><?= e($user['address'] ?? '') ?></textarea>
            </label>
            <label>Ghi chú
                <textarea name="note" rows="2"></textarea>
            </label>

            <h3>Phương thức thanh toán</h3>
            <label class="radio-row">
                <input type="radio" name="payment_method" value="cod" checked> Thanh toán khi nhận hàng (COD)
            </label>
            <label class="radio-row">
                <input type="radio" name="payment_method" value="vnpay"> Thanh toán online qua VNPay
            </label>
        </div>

        <aside class="checkout-summary">
            <h3>Đơn hàng</h3>
            <?php foreach ($items as $it): ?>
                <div class="co-row">
                    <span><?= e($it['product_name']) ?> (<?= e($it['size']) ?>/<?= e($it['color']) ?>) ×<?= (int) $it['quantity'] ?></span>
                    <span><?= vnd($it['price'] * $it['quantity']) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="co-total">
                <span>Tổng cộng</span>
                <strong><?= vnd($total) ?></strong>
            </div>
            <button type="submit" class="btn btn-dark btn-block">Đặt hàng</button>
        </aside>
    </form>
</section>
