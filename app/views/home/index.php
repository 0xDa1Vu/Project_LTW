<?php
$heroVideo = dirname(__DIR__, 3) . '/public/assets/hero.mp4';
$bodyClass = 'home';
?>

<!-- HERO VIDEO -->
<section class="hero">
    <?php if (is_file($heroVideo)): ?>
        <video class="hero-video" autoplay muted loop playsinline poster="/assets/hero-poster.svg">
            <source src="/assets/hero.mp4" type="video/mp4">
        </video>
    <?php else: ?>
        <img class="hero-video" src="/assets/hero-poster.svg" alt="" aria-hidden="true">
    <?php endif; ?>
    <div class="hero-overlay">
        <div class="hero-corner">
            <span class="hero-watermark">ATELIER</span>
            <a href="/products" class="hero-btn">KHÁM PHÁ</a>
        </div>
    </div>
</section>


<!-- NEW ARRIVAL / BEST SELLER CAROUSEL -->
<section class="section section-carousel">
    <div class="carousel-header container">
        <div class="carousel-tabs">
            <button class="carousel-tab active" data-tab="new">NEW ARRIVAL</button>
            <button class="carousel-tab" data-tab="best">BEST SELLER</button>
        </div>
        <a href="/products" class="btn-view-all">Xem tất cả →</a>
    </div>

    <!-- New Arrival -->
    <div class="carousel-wrapper" id="tabNew">
        <button class="carousel-btn carousel-prev" aria-label="Trước">&#8249;</button>
        <div class="carousel-track-outer">
            <div class="carousel-track" id="trackNew">
                <?php foreach ($featured as $p): ?>
                    <?php require dirname(__DIR__) . '/partials/product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <button class="carousel-btn carousel-next" aria-label="Tiếp">&#8250;</button>
    </div>

    <!-- Best Seller -->
    <div class="carousel-wrapper" id="tabBest" hidden>
        <button class="carousel-btn carousel-prev" aria-label="Trước">&#8249;</button>
        <div class="carousel-track-outer">
            <div class="carousel-track" id="trackBest">
                <?php foreach ($bestSellers as $p): ?>
                    <?php require dirname(__DIR__) . '/partials/product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <button class="carousel-btn carousel-next" aria-label="Tiếp">&#8250;</button>
    </div>
</section>

<!-- LIFESTYLE GRID — ÁO -->
<section class="section section-lifestyle">
    <div class="lifestyle-header container">
        <h2 class="lifestyle-title">ÁO</h2>
    </div>
    <div class="lifestyle-grid container">
        <a href="/products?category=6" class="lifestyle-card">
            <div class="lifestyle-img" style="background-image:url('/assets/life-ao-thun.jpg')"></div>
            <p class="lifestyle-caption">Áo thun</p>
        </a>
        <a href="/products?category=7" class="lifestyle-card">
            <div class="lifestyle-img" style="background-image:url('/assets/life-ao-somi.jpg')"></div>
            <p class="lifestyle-caption">Áo sơ mi</p>
        </a>
        <a href="/products?category=8" class="lifestyle-card">
            <div class="lifestyle-img" style="background-image:url('/assets/life-ao-khoac.jpg')"></div>
            <p class="lifestyle-caption">Áo khoác</p>
        </a>
        <a href="/products?category=9" class="lifestyle-card">
            <div class="lifestyle-img" style="background-image:url('/assets/life-hoodie.jpg')"></div>
            <p class="lifestyle-caption">Hoodie</p>
        </a>
    </div>
</section>

<!-- LIFESTYLE GRID — QUẦN -->
<section class="section section-lifestyle section-lifestyle--alt">
    <div class="lifestyle-header container">
        <h2 class="lifestyle-title">QUẦN</h2>
    </div>
    <div class="lifestyle-grid container">
        <a href="/products?category=10" class="lifestyle-card">
            <div class="lifestyle-img" style="background-image:url('/assets/life-quan-jean.jpg')"></div>
            <p class="lifestyle-caption">Quần jean</p>
        </a>
        <a href="/products?category=11" class="lifestyle-card">
            <div class="lifestyle-img" style="background-image:url('/assets/life-quan-jogger.jpg')"></div>
            <p class="lifestyle-caption">Quần jogger</p>
        </a>
        <a href="/products?category=12" class="lifestyle-card">
            <div class="lifestyle-img" style="background-image:url('/assets/life-quan-short.jpg')"></div>
            <p class="lifestyle-caption">Quần short</p>
        </a>
    </div>
</section>

<!-- LIFESTYLE GRID — PHỤ KIỆN -->
<section class="section section-lifestyle">
    <div class="lifestyle-header container">
        <h2 class="lifestyle-title">PHỤ KIỆN</h2>
    </div>
    <div class="lifestyle-grid container">
        <a href="/products?category=13" class="lifestyle-card">
            <div class="lifestyle-img" style="background-image:url('/assets/life-mu.jpg')"></div>
            <p class="lifestyle-caption">Mũ</p>
        </a>
        <a href="/products?category=14" class="lifestyle-card">
            <div class="lifestyle-img" style="background-image:url('/assets/life-tui.jpg')"></div>
            <p class="lifestyle-caption">Túi</p>
        </a>
        <a href="/products?category=15" class="lifestyle-card">
            <div class="lifestyle-img" style="background-image:url('/assets/life-tat.jpg')"></div>
            <p class="lifestyle-caption">Tất</p>
        </a>
    </div>
</section>
