<?php
/** Cần biến $p (1 sản phẩm có cột image, name, slug, price, sale_price) */
$price = $p['sale_price'] !== null && $p['sale_price'] !== '' ? $p['sale_price'] : $p['price'];
?>
<a href="/product/<?= e($p['slug']) ?>" class="product-card">
    <div class="product-thumb">
        <?php if (!empty($p['image'])): ?>
            <img src="<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
        <?php else: ?>
            <div class="thumb-placeholder">No image</div>
        <?php endif; ?>
        <?php if (!empty($bestSellerIds) && in_array((int) $p['id'], $bestSellerIds, true)): ?>
            <span class="badge-bestseller">BEST SELLER</span>
        <?php elseif ($p['sale_price'] !== null && $p['sale_price'] !== ''): ?>
            <span class="badge-sale">SALE</span>
        <?php endif; ?>
    </div>
    <div class="product-info">
        <h3 class="product-name"><?= e($p['name']) ?></h3>
        <div class="product-price">
            <span class="price-now"><?= vnd($price) ?></span>
            <?php if ($p['sale_price'] !== null && $p['sale_price'] !== ''): ?>
                <span class="price-old"><?= vnd($p['price']) ?></span>
            <?php endif; ?>
        </div>
    </div>
</a>
