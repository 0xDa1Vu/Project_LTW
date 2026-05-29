<!-- HERO VIDEO full-width -->
<?php
// Chỉ render <video> nếu hero.mp4 thực sự tồn tại; nếu không, nền gradient (.hero)
// + poster SVG hiển thị đẹp như banner tĩnh.
$heroVideo = dirname(__DIR__, 3) . '/public/assets/hero.mp4';
?>
<section class="hero">
    <?php if (is_file($heroVideo)): ?>
        <video class="hero-video" autoplay muted loop playsinline poster="/assets/hero-poster.svg">
            <source src="/assets/hero.mp4" type="video/mp4">
        </video>
    <?php else: ?>
        <img class="hero-video" src="/assets/hero-poster.svg" alt="" aria-hidden="true">
    <?php endif; ?>
    <div class="hero-overlay">
        <div class="hero-content">
            <h1 class="hero-title">ATELIER</h1>
            <p class="hero-sub">Bộ sưu tập thời trang tối giản · hiện đại</p>
            <a href="/products" class="btn btn-light">Khám phá ngay</a>
        </div>
    </div>
</section>

<!-- Danh mục -->
<section class="container section">
    <h2 class="section-title">Danh mục</h2>
    <div class="category-grid">
        <?php foreach ($categories as $c): ?>
            <a href="/products?category=<?= (int) $c['id'] ?>" class="category-card">
                <span><?= e($c['name']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Sản phẩm mới -->
<section class="container section">
    <h2 class="section-title">Sản phẩm mới</h2>
    <div class="product-grid">
        <?php foreach ($featured as $p): ?>
            <?php require dirname(__DIR__) . '/partials/product_card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<!-- Bán chạy -->
<section class="container section">
    <h2 class="section-title">Bán chạy nhất</h2>
    <div class="product-grid">
        <?php foreach ($bestSellers as $p): ?>
            <?php require dirname(__DIR__) . '/partials/product_card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>
