<section class="container section">
    <h1 class="section-title">Sản phẩm</h1>
    <div class="shop-layout">
        <!-- Sidebar lọc -->
        <aside class="filter-sidebar">
            <form method="get" action="/products" id="filterForm">
                <?php if (!empty($filters['q'])): ?>
                    <input type="hidden" name="q" value="<?= e($filters['q']) ?>">
                <?php endif; ?>

                <div class="filter-group">
                    <h4>Danh mục</h4>
                    <select name="category">
                        <option value="">Tất cả</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= (string)($filters['category_id'] ?? '') === (string)$c['id'] ? 'selected' : '' ?>>
                                <?= e($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <h4>Khoảng giá</h4>
                    <div class="price-range">
                        <input type="number" name="min_price" placeholder="Từ" value="<?= e($filters['min_price']) ?>">
                        <input type="number" name="max_price" placeholder="Đến" value="<?= e($filters['max_price']) ?>">
                    </div>
                </div>

                <div class="filter-group">
                    <h4>Kích cỡ</h4>
                    <select name="size">
                        <option value="">Tất cả</option>
                        <?php foreach ($sizes as $s): ?>
                            <option value="<?= e($s) ?>" <?= ($filters['size'] ?? '') === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <h4>Màu sắc</h4>
                    <select name="color">
                        <option value="">Tất cả</option>
                        <?php foreach ($colors as $col): ?>
                            <option value="<?= e($col) ?>" <?= ($filters['color'] ?? '') === $col ? 'selected' : '' ?>><?= e($col) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-dark btn-block">Lọc</button>
                <a href="/products" class="btn btn-block btn-outline">Xoá lọc</a>
            </form>
        </aside>

        <!-- Danh sách -->
        <div class="shop-main">
            <div class="shop-toolbar">
                <span><?= (int) $result['total'] ?> sản phẩm</span>
                <form method="get" action="/products" class="sort-form">
                    <?php foreach (['category','q','min_price','max_price','size','color'] as $k):
                        $val = $filters[str_replace('category','category_id',$k)] ?? ($filters[$k] ?? '');
                        if ($k === 'category') $val = $filters['category_id'] ?? '';
                        if ($val !== '' && $val !== null): ?>
                            <input type="hidden" name="<?= $k ?>" value="<?= e($val) ?>">
                    <?php endif; endforeach; ?>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="">Mới nhất</option>
                        <option value="price_asc"  <?= ($filters['sort'] ?? '')==='price_asc'?'selected':'' ?>>Giá tăng dần</option>
                        <option value="price_desc" <?= ($filters['sort'] ?? '')==='price_desc'?'selected':'' ?>>Giá giảm dần</option>
                        <option value="name"        <?= ($filters['sort'] ?? '')==='name'?'selected':'' ?>>Tên A→Z</option>
                    </select>
                </form>
            </div>

            <?php if (empty($result['items'])): ?>
                <p class="empty-note">Không tìm thấy sản phẩm phù hợp.</p>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($result['items'] as $p): ?>
                        <?php require dirname(__DIR__) . '/partials/product_card.php'; ?>
                    <?php endforeach; ?>
                </div>

                <!-- Phân trang -->
                <?php if ($result['pages'] > 1): ?>
                    <nav class="pagination">
                        <?php
                        $qs = $_GET;
                        for ($i = 1; $i <= $result['pages']; $i++):
                            $qs['page'] = $i;
                        ?>
                            <a href="/products?<?= e(http_build_query($qs)) ?>"
                               class="<?= $i === $result['page'] ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
