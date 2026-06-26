<?php
use App\Core\Auth;
use App\Core\Csrf;
$price = $product['sale_price'] !== null && $product['sale_price'] !== '' ? $product['sale_price'] : $product['price'];
?>
<section class="container section product-detail">
    <div class="pd-gallery">
        <div class="pd-main-image" style="position:relative">
            <?php if (!empty($images)): ?>
                <img id="mainImage" src="<?= e($images[0]['image_url']) ?>" alt="<?= e($product['name']) ?>">
                <?php if (count($images) > 1): ?>
                    <button class="pd-nav pd-prev" onclick="pdNav(-1)" aria-label="Ảnh trước"><svg width="10" height="18" viewBox="0 0 10 18" fill="none"><polyline points="9,1 1,9 9,17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                    <button class="pd-nav pd-next" onclick="pdNav(1)" aria-label="Ảnh tiếp"><svg width="10" height="18" viewBox="0 0 10 18" fill="none"><polyline points="1,1 9,9 1,17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                <?php endif; ?>
            <?php else: ?>
                <div class="thumb-placeholder">No image</div>
            <?php endif; ?>
        </div>
        <?php if (count($images) > 1): ?>
            <div class="pd-thumbs">
                <?php foreach ($images as $i => $img): ?>
                    <img src="<?= e($img['image_url']) ?>" class="pd-thumb <?= $i === 0 ? 'active' : '' ?>" alt="thumb"
                         onclick="pdSet(<?= $i ?>)">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php if (!empty($images)): ?>
    <script>
    (function(){
        var imgs = <?= json_encode(array_column($images, 'image_url')) ?>;
        var cur = 0;
        function update(){
            document.getElementById('mainImage').src = imgs[cur];
            document.querySelectorAll('.pd-thumb').forEach(function(el, i){ el.classList.toggle('active', i===cur); });
        }
        window.pdNav = function(d){ cur = (cur + d + imgs.length) % imgs.length; update(); };
        window.pdSet = function(i){ cur = i; update(); };
    })();
    </script>
    <?php endif; ?>

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

        <?php
            // Build color → sizes map
            $colorMap = [];
            $colorList = [];
            foreach ($variants as $v) {
                $c = $v['color'];
                $s = $v['size'];
                if (!isset($colorMap[$c])) {
                    $colorMap[$c] = [];
                    $colorList[] = $c;
                }
                $colorMap[$c][$s] = ['id' => $v['id'], 'stock' => $v['stock']];
            }
            $firstColor = $colorList[0] ?? null;
        ?>
        <form id="addToCartForm" class="pd-form">
            <?= Csrf::field() ?>
            <input type="hidden" name="variant_id" id="variantId" value="" required>

            <?php if (!empty($colorList)): ?>
            <div class="pd-field">
                <label>Màu sắc: <span id="selectedColor" class="pd-selected-label"><?= e($firstColor) ?></span></label>
                <div class="pd-color-group" id="colorGroup">
                    <?php foreach ($colorList as $i => $c): ?>
                        <button type="button" class="pd-color-btn <?= $i === 0 ? 'active' : '' ?>"
                                data-color="<?= e($c) ?>"><?= e($c) ?></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pd-field">
                <label>Size: <span id="selectedSize" class="pd-selected-label"></span></label>
                <div class="pd-size-group" id="sizeGroup"></div>
            </div>
            <?php endif; ?>

            <div class="pd-field pd-qty-field">
                <label>Số lượng</label>
                <div class="pd-qty-wrap">
                    <button type="button" class="pd-qty-btn" id="qtyMinus">−</button>
                    <input type="number" name="quantity" id="qtyInput" value="1" min="1" class="qty-input" readonly>
                    <button type="button" class="pd-qty-btn" id="qtyPlus">+</button>
                </div>
            </div>
            <button type="submit" class="btn btn-dark btn-block">Thêm vào giỏ</button>
        </form>

        <script>
        (function(){
            var variants = <?= json_encode(array_map(fn($v) => [
                'id'    => (int)$v['id'],
                'color' => $v['color'],
                'size'  => $v['size'],
                'stock' => (int)$v['stock'],
            ], $variants)) ?>;

            var selectedColor = null, selectedSize = null;
            var colorBtns = document.querySelectorAll('.pd-color-btn');
            var sizeGroup = document.getElementById('sizeGroup');
            var variantIdInput = document.getElementById('variantId');
            var selectedColorLabel = document.getElementById('selectedColor');
            var selectedSizeLabel = document.getElementById('selectedSize');

            function getSizesForColor(color) {
                return variants.filter(function(v){ return v.color === color; });
            }

            function renderSizes(color) {
                var rows = getSizesForColor(color);
                sizeGroup.innerHTML = '';
                selectedSize = null;
                selectedSizeLabel.textContent = '';
                variantIdInput.value = '';
                rows.forEach(function(v) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'pd-size-btn' + (v.stock < 1 ? ' sold-out' : '');
                    btn.textContent = v.size;
                    btn.dataset.size = v.size;
                    btn.dataset.id = v.id;
                    btn.disabled = v.stock < 1;
                    btn.addEventListener('click', function(){
                        if (v.stock < 1) return;
                        sizeGroup.querySelectorAll('.pd-size-btn').forEach(function(b){ b.classList.remove('active'); });
                        btn.classList.add('active');
                        selectedSize = v.size;
                        selectedSizeLabel.textContent = v.size;
                        variantIdInput.value = v.id;
                        document.getElementById('qtyInput').max = v.stock;
                    });
                    sizeGroup.appendChild(btn);
                });
            }

            colorBtns.forEach(function(btn){
                btn.addEventListener('click', function(){
                    colorBtns.forEach(function(b){ b.classList.remove('active'); });
                    btn.classList.add('active');
                    selectedColor = btn.dataset.color;
                    selectedColorLabel.textContent = selectedColor;
                    renderSizes(selectedColor);
                });
            });

            // init
            if (colorBtns.length) {
                selectedColor = colorBtns[0].dataset.color;
                renderSizes(selectedColor);
            }

            // qty buttons
            document.getElementById('qtyMinus').addEventListener('click', function(){
                var inp = document.getElementById('qtyInput');
                if (parseInt(inp.value) > 1) inp.value = parseInt(inp.value) - 1;
            });
            document.getElementById('qtyPlus').addEventListener('click', function(){
                var inp = document.getElementById('qtyInput');
                var max = parseInt(inp.max) || 999;
                if (parseInt(inp.value) < max) inp.value = parseInt(inp.value) + 1;
            });

            document.getElementById('addToCartForm').addEventListener('submit', function(e){
                if (!variantIdInput.value) {
                    e.preventDefault();
                    alert('Vui lòng chọn size.');
                }
            });
        })();
        </script>

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
