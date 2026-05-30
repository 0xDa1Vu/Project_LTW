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
<body class="<?= isset($bodyClass) ? e($bodyClass) : '' ?>">
<header class="site-header" id="siteHeader">
    <div class="container header-inner">
        <button class="nav-toggle" id="navToggle" aria-label="Mở menu">☰</button>
        <nav class="main-nav" id="mainNav">
            <a href="/products">SHOP</a>
            <a href="/about">ABOUT</a>
            <a href="/care">WHENEVER CARE</a>
            <a href="/faq">FAQ</a>
        </nav>
        <a href="/" class="logo">ATELIER</a>
        <div class="header-actions">
            <button type="button" class="header-link" id="searchToggle" aria-label="Tìm kiếm">SEARCH</button>
            <?php if (Auth::check()): ?>
                <div class="dropdown">
                    <button class="dropdown-btn header-link">ACCOUNT</button>
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
                <a href="/login" class="header-link">ACCOUNT</a>
            <?php endif; ?>
            <a href="/cart" class="header-link cart-link">CART (<span class="cart-count" id="cartCount">0</span>)</a>
        </div>
    </div>
</header>

<?php
// Gợi ý tìm kiếm = tên các danh mục (lấy động từ DB)
$searchSuggestions = [];
try {
    foreach ((new \App\Models\Category())->allOrdered() as $cat) {
        $searchSuggestions[] = $cat['name'];
    }
} catch (\Throwable $e) { /* nếu DB lỗi thì panel vẫn mở, chỉ không có gợi ý */ }
?>
<!-- SEARCH DRAWER: panel trượt từ phải -->
<div class="search-overlay" id="searchOverlay" hidden></div>
<aside class="search-drawer" id="searchDrawer" hidden aria-label="Tìm kiếm">
    <div class="search-drawer-head">
        <h2>search</h2>
        <button type="button" class="search-close" id="searchClose" aria-label="Đóng">&times;</button>
    </div>
    <form action="/products" method="get">
        <input type="search" name="q" class="search-drawer-input" placeholder="Type here"
               value="<?= e($_GET['q'] ?? '') ?>">
    </form>
    <?php if ($searchSuggestions): ?>
        <p class="search-sg-label">suggestions:</p>
        <ul class="search-sg-list">
            <?php foreach ($searchSuggestions as $sg): ?>
                <li><a href="/products?q=<?= urlencode($sg) ?>"><?= e($sg) ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</aside>

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
