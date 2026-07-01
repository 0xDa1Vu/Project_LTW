<section class="section">
    <h1 class="section-title container">Sản phẩm</h1>
    <div class="shop-layout">
        <!-- Sidebar lọc -->
        <aside class="filter-sidebar">
            <form method="get" action="/products" id="filterForm">
                <?php if (!empty($filters['q'])): ?>
                    <input type="hidden" name="q" value="<?= e($filters['q']) ?>">
                <?php endif; ?>
                <?php
                $priceBuckets = [
                    ['label' => '0 - 500K',     'min' => 0,    'max' => 500000],
                    ['label' => '500K - 1000K', 'min' => 500000, 'max' => 1000000],
                    ['label' => '1000K - 2000K', 'min' => 1000000, 'max' => 2000000],
                    ['label' => 'Trên 2000K',    'min' => 2000000, 'max' => ''],
                ];
                $selectedSizes  = (array) ($filters['size'] ?? []);
                $selectedColors = (array) ($filters['color'] ?? []);
                ?>

                <details class="filter-group" open>
                    <summary>Danh mục</summary>
                    <select name="category">
                        <option value="">Tất cả</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= (string)($filters['category_id'] ?? '') === (string)$c['id'] ? 'selected' : '' ?>>
                                <?= e($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </details>

                <details class="filter-group">
                    <summary>Màu sắc</summary>
                    <div class="checkbox-list">
                        <?php foreach ($colors as $col): ?>
                            <label class="checkbox-item">
                                <input type="checkbox" name="color[]" value="<?= e($col) ?>" <?= in_array($col, $selectedColors, true) ? 'checked' : '' ?>>
                                <?= e($col) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </details>

                <details class="filter-group">
                    <summary>Kích cỡ</summary>
                    <div class="checkbox-list">
                        <?php foreach ($sizes as $s): ?>
                            <label class="checkbox-item">
                                <input type="checkbox" name="size[]" value="<?= e($s) ?>" <?= in_array($s, $selectedSizes, true) ? 'checked' : '' ?>>
                                <?= e($s) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </details>

                <details class="filter-group" open>
                    <summary>Khoảng giá</summary>
                    <input type="hidden" name="min_price" value="<?= e($filters['min_price']) ?>">
                    <input type="hidden" name="max_price" value="<?= e($filters['max_price']) ?>">
                    <div class="price-buckets">
                        <?php foreach ($priceBuckets as $b):
                            $active = (string)$filters['min_price'] === (string)$b['min'] && (string)$filters['max_price'] === (string)$b['max'];
                        ?>
                            <button type="button" class="price-bucket-btn <?= $active ? 'active' : '' ?>"
                                data-min="<?= e($b['min']) ?>" data-max="<?= e($b['max']) ?>"><?= e($b['label']) ?></button>
                        <?php endforeach; ?>
                    </div>
                </details>

                <button type="submit" class="btn btn-dark btn-block">Lọc</button>
                <a href="/products" class="btn btn-block btn-outline">Xoá lọc</a>
            </form>
        </aside>

        <!-- Danh sách -->
        <div class="shop-main">
            <div class="shop-toolbar">
                <span><?= (int) $result['total'] ?> sản phẩm</span>
                <form method="get" action="/products" class="sort-form">
                    <?php foreach (['category','q','min_price','max_price'] as $k):
                        $val = $k === 'category' ? ($filters['category_id'] ?? '') : ($filters[$k] ?? '');
                        if ($val !== '' && $val !== null): ?>
                            <input type="hidden" name="<?= $k ?>" value="<?= e($val) ?>">
                    <?php endif; endforeach; ?>
                    <?php foreach ((array) ($filters['size'] ?? []) as $s): ?>
                        <input type="hidden" name="size[]" value="<?= e($s) ?>">
                    <?php endforeach; ?>
                    <?php foreach ((array) ($filters['color'] ?? []) as $col): ?>
                        <input type="hidden" name="color[]" value="<?= e($col) ?>">
                    <?php endforeach; ?>
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

<script>
document.querySelectorAll('.price-bucket-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var form = document.getElementById('filterForm');
        form.querySelector('input[name="min_price"]').value = btn.dataset.min;
        form.querySelector('input[name="max_price"]').value = btn.dataset.max;
        form.submit();
    });
});
</script>
