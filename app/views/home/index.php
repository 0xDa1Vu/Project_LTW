<?php
$heroVideo = dirname(__DIR__, 3) . '/public/assets/hero.mp4';
$fantasyVideo = dirname(__DIR__, 3) . '/public/assets/fantasy.mp4';
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
    <div class="carousel-wrapper" data-carousel-panel="new">
        <button class="carousel-btn carousel-prev" aria-label="Trước">&#8249;</button>
        <div class="carousel-track-outer">
            <div class="carousel-track">
                <?php foreach ($featured as $p): ?>
                    <?php require dirname(__DIR__) . '/partials/product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <button class="carousel-btn carousel-next" aria-label="Tiếp">&#8250;</button>
    </div>

    <!-- Best Seller -->
    <div class="carousel-wrapper" data-carousel-panel="best" hidden>
        <button class="carousel-btn carousel-prev" aria-label="Trước">&#8249;</button>
        <div class="carousel-track-outer">
            <div class="carousel-track">
                <?php foreach ($bestSellers as $p): ?>
                    <?php require dirname(__DIR__) . '/partials/product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <button class="carousel-btn carousel-next" aria-label="Tiếp">&#8250;</button>
    </div>
</section>

<?php $stylingsWithCover = array_filter($stylings, fn($s) => !empty($stylingCovers[$s['id']])); ?>
<?php if (!empty($stylingsWithCover)): ?>
<!-- STYLING LOOKBOOK -->
<section class="section section-styling">
    <div class="styling-header container">
        <h2 class="styling-title">STYLING</h2>
        <a href="/products" class="btn-view-all">Xem tất cả →</a>
    </div>
    <div class="carousel-wrapper" id="stylingWrapper">
        <button class="carousel-btn carousel-prev" aria-label="Trước">&#8249;</button>
        <div class="carousel-track-outer">
            <div class="carousel-track" id="stylingTrack">
                <?php foreach ($stylingsWithCover as $s): ?>
                    <figure class="styling-card">
                        <a href="/styling/<?= (int) $s['id'] ?>" class="styling-card-img">
                            <img src="<?= e($stylingCovers[$s['id']]) ?>" alt="<?= e($s['title']) ?>" loading="lazy">
                        </a>
                        <div class="styling-card-info">
                            <figcaption><?= e($s['title']) ?></figcaption>
                            <a href="/styling/<?= (int) $s['id'] ?>" class="styling-view-btn">Xem bộ phối</a>
                        </div>
                    </figure>
                <?php endforeach; ?>
            </div>
        </div>
        <button class="carousel-btn carousel-next" aria-label="Tiếp">&#8250;</button>
    </div>
</section>
<?php endif; ?>

<!-- FANTASY COLLECTION -->
<section class="section section-fantasy">
    <div class="fantasy-media">
        <?php if (is_file($fantasyVideo)): ?>
            <video class="fantasy-video" autoplay muted loop playsinline>
                <source src="/assets/fantasy.mp4" type="video/mp4">
            </video>
        <?php else: ?>
            <img class="fantasy-video" src="/assets/hero-poster.svg" alt="" aria-hidden="true">
        <?php endif; ?>
    </div>
    <div class="fantasy-content">
        <h2 class="fantasy-title">FANTASY COLLECTION.</h2>
        <p class="fantasy-desc">Fantasy Collection is where imagination meets comfort. Celebrating softness, playfulness, and boundless creativity, the collection brings Hello Kitty's iconic charm into Levents' fantastical universe. Each piece blends everyday comfort with a subtle touch of gentle, expressive, and effortlessly wearable. Designed for dreamers, this collection encourages us to embrace joy, trust ourselves, and follow our dreams, every single day</p>
        <a href="/products" class="fantasy-btn">XEM THÊM</a>
    </div>
</section>
