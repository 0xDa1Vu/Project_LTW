<?php use App\Core\Csrf; ?>
<section class="container section">
    <h1 class="section-title">Giỏ hàng</h1>
    <?php if (empty($items)): ?>
        <p class="empty-note">Giỏ hàng trống. <a href="/products">Tiếp tục mua sắm →</a></p>
    <?php else: ?>
        <div class="cart-layout">
            <div class="cart-items" data-csrf="<?= Csrf::token() ?>">
                <?php foreach ($items as $it): ?>
                    <div class="cart-row" data-id="<?= (int) $it['cart_item_id'] ?>" data-price="<?= (float) $it['price'] ?>">
                        <div class="cart-thumb">
                            <?php if (!empty($it['image'])): ?>
                                <img src="<?= e($it['image']) ?>" alt="<?= e($it['product_name']) ?>">
                            <?php endif; ?>
                        </div>
                        <div class="cart-detail">
                            <a href="/product/<?= e($it['slug']) ?>" class="cart-name"><?= e($it['product_name']) ?></a>
                            <span class="cart-variant"><?= e($it['size']) ?> / <?= e($it['color']) ?></span>
                            <span class="cart-unit"><?= vnd($it['price']) ?></span>
                        </div>
                        <div class="cart-qty">
                            <input type="number" min="1" value="<?= (int) $it['quantity'] ?>" class="qty-input cart-qty-input">
                        </div>
                        <div class="cart-line"><?= vnd($it['price'] * $it['quantity']) ?></div>
                        <button class="cart-remove" aria-label="Xoá">✕</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <aside class="cart-summary">
                <h3>Tổng cộng</h3>
                <div class="summary-row">
                    <span>Tạm tính</span>
                    <strong id="cartTotal"><?= vnd($total) ?></strong>
                </div>
                <a href="/checkout" class="btn btn-dark btn-block">Tiến hành thanh toán</a>
                <a href="/products" class="btn btn-outline btn-block">Tiếp tục mua</a>
            </aside>
        </div>
    <?php endif; ?>
</section>
