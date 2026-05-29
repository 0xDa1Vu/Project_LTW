<?php
use App\Core\Auth;
use App\Core\Session;
$flash = Session::takeFlash();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? e($title) . ' — ' : '' ?>ATELIER · Thời trang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<header class="site-header" id="siteHeader">
    <div class="container header-inner">
        <a href="/" class="logo">ATELIER</a>
        <button class="nav-toggle" id="navToggle" aria-label="Mở menu">☰</button>
        <nav class="main-nav" id="mainNav">
            <a href="/">Trang chủ</a>
            <a href="/products">Sản phẩm</a>
            <form class="search-form" action="/products" method="get">
                <input type="search" name="q" placeholder="Tìm sản phẩm..." value="<?= e($_GET['q'] ?? '') ?>">
                <button type="submit" aria-label="Tìm">⌕</button>
            </form>
        </nav>
        <div class="header-actions">
            <a href="/cart" class="cart-link" aria-label="Giỏ hàng">
                🛍 <span class="cart-count" id="cartCount">0</span>
            </a>
            <?php if (Auth::check()): ?>
                <div class="dropdown">
                    <button class="dropdown-btn"><?= e(Session::get('user_name')) ?> ▾</button>
                    <div class="dropdown-menu">
                        <a href="/account">Tài khoản</a>
                        <a href="/account/orders">Đơn hàng</a>
                        <?php if (Auth::isAdmin()): ?>
                            <a href="/admin">Quản trị</a>
                        <?php endif; ?>
                        <form action="/logout" method="post">
                            <?= \App\Core\Csrf::field() ?>
                            <button type="submit" class="link-btn">Đăng xuất</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <a href="/login" class="btn-text">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if ($flash): ?>
<div class="toast-stack" id="toastStack">
    <?php foreach ($flash as $f): ?>
        <div class="toast toast-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<main>
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <h3 class="logo">ATELIER</h3>
            <p>Thời trang tối giản, hiện đại.</p>
        </div>
        <div>
            <h4>Liên kết</h4>
            <a href="/products">Sản phẩm</a>
            <a href="/cart">Giỏ hàng</a>
            <a href="/account">Tài khoản</a>
        </div>
        <div>
            <h4>Liên hệ</h4>
            <p>Đồ án Lập trình Web</p>
            <p>PHP · PostgreSQL</p>
        </div>
    </div>
    <div class="footer-bottom">© <?= date('Y') ?> ATELIER. Đồ án môn học.</div>
</footer>

<script src="/js/cart.js"></script>
<script src="/js/main.js"></script>
</body>
</html>
