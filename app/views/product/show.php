<?php
use App\Core\Auth;
use App\Core\Csrf;
$price = $product['sale_price'] !== null && $product['sale_price'] !== '' ? $product['sale_price'] : $product['price'];
?>
<section class="container section product-detail">
    <div class="pd-gallery">
        <div class="pd-main-image">
            <?php if (!empty($images)): ?>
                <img id="mainImage" src="<?= e($images[0]['image_url']) ?>" alt="<?= e($product['name']) ?>">
            <?php else: ?>
                <div class="thumb-placeholder">No image</div>
            <?php endif; ?>
        </div>
        <?php if (count($images) > 1): ?>
            <div class="pd-thumbs">
                <?php foreach ($images as $img): ?>
                    <img src="<?= e($img['image_url']) ?>" class="pd-thumb" alt="thumb"
                         onclick="document.getElementById('mainImage').src=this.src">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="pd-info">
        <?php if (!empty($product['category_name'])): ?>
            <a class="pd-cat" href="/products?category=<?= (int) $product['category_id'] ?>"><?= e($product['category_name']) ?></a>
        <?php endif; ?>
        <h1 class="pd-title"><?= e($product['name']) ?></h1>
        <div class="pd-rating">
            ★ <?= number_format((float) $summary['avg'], 1) ?> (<?= (int) $summary['cnt'] ?> đánh giá)
        </div>
        <div class="pd-price">
            <span class="price-now"><?= vnd($price) ?></span>
            <?php if ($product['sale_price'] !== null && $product['sale_price'] !== ''): ?>
                <span class="price-old"><?= vnd($product['price']) ?></span>
            <?php endif; ?>
        </div>

        <form id="addToCartForm" class="pd-form">
            <?= Csrf::field() ?>
            <div class="pd-field">
                <label>Phân loại</label>
                <select name="variant_id" id="variantSelect" required>
                    <option value="">-- Chọn size / màu --</option>
                    <?php foreach ($variants as $v): ?>
                        <option value="<?= (int) $v['id'] ?>" <?= $v['stock'] < 1 ? 'disabled' : '' ?>>
                            <?= e($v['size']) ?> / <?= e($v['color']) ?>
                            <?= $v['stock'] < 1 ? '(hết hàng)' : '(còn ' . (int) $v['stock'] . ')' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="pd-field">
                <label>Số lượng</label>
                <input type="number" name="quantity" value="1" min="1" class="qty-input">
            </div>
            <button type="submit" class="btn btn-dark btn-block">Thêm vào giỏ</button>
        </form>

        <div class="pd-description">
            <h3>Mô tả</h3>
            <p><?= nl2br(e($product['description'] ?? 'Đang cập nhật.')) ?></p>
        </div>
    </div>
</section>

<!-- Đánh giá -->
<section class="container section">
    <h2 class="section-title">Đánh giá sản phẩm</h2>

    <?php if (Auth::check()): ?>
        <form action="/review" method="post" class="review-form">
            <?= Csrf::field() ?>
            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
            <div class="review-stars">
                <label>Chấm điểm:</label>
                <select name="rating" required>
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?= $i ?>"><?= str_repeat('★', $i) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <textarea name="comment" placeholder="Chia sẻ cảm nhận của bạn..." rows="3"></textarea>
            <button type="submit" class="btn btn-dark">Gửi đánh giá</button>
        </form>
    <?php else: ?>
        <p><a href="/login">Đăng nhập</a> để viết đánh giá.</p>
    <?php endif; ?>

    <div class="review-list">
        <?php if (empty($reviews)): ?>
            <p class="empty-note">Chưa có đánh giá nào.</p>
        <?php else: foreach ($reviews as $r): ?>
            <div class="review-item">
                <div class="review-head">
                    <strong><?= e($r['user_name']) ?></strong>
                    <span class="review-rating"><?= str_repeat('★', (int) $r['rating']) ?></span>
                </div>
                <p><?= nl2br(e($r['comment'])) ?></p>
            </div>
        <?php endforeach; endif; ?>
    </div>
</section>
