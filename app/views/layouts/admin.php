<?php
use App\Core\Session;
$flash = Session::takeFlash();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — <?= isset($title) ? e($title) : 'Quản trị' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body class="admin-body">
<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-logo">ATELIER<span>admin</span></div>
    <nav>
        <a href="/admin">Dashboard</a>
        <a href="/admin/products">Sản phẩm</a>
        <a href="/admin/categories">Danh mục</a>
        <a href="/admin/orders">Đơn hàng</a>
        <a href="/admin/users">Người dùng</a>
        <a href="/admin/reviews">Đánh giá</a>
        <a href="/" class="back-site">← Về trang web</a>
    </nav>
</aside>
<div class="admin-main">
    <header class="admin-topbar">
        <button class="admin-menu-toggle" id="adminMenuToggle">☰</button>
        <h1><?= isset($title) ? e($title) : 'Quản trị' ?></h1>
        <div class="admin-user"><?= e(Session::get('user_name')) ?></div>
    </header>

    <?php if ($flash): ?>
    <div class="toast-stack" id="toastStack">
        <?php foreach ($flash as $f): ?>
            <div class="toast toast-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="admin-content">
        <?= $content ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script src="/js/admin.js"></script>
</body>
</html>
