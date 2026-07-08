<?php use App\Core\Auth; use App\Core\Csrf; ?>
<div class="co2-wrap">
  <form action="/checkout" method="post" class="co2-layout">
    <?= Csrf::field() ?>

    <!-- LEFT COLUMN -->
    <div class="co2-main">

      <?php if (!Auth::id()): ?>
      <div class="co2-card">
        <div class="co2-login-banner">
          <p>Đăng nhập để mua hàng tiện lợi và nhận nhiều ưu đãi hơn.</p>
          <a href="/login" class="co2-login-btn">Đăng nhập</a>
        </div>
      </div>
      <?php endif; ?>

      <!-- Thông tin giao hàng -->
      <div class="co2-card">
        <h2 class="co2-section-title">Thông tin giao hàng</h2>
        <div class="co2-field-group">
          <input type="text" name="name" placeholder="Họ và tên"
                 value="<?= e($user['name'] ?? '') ?>" required class="co2-input">
          <input type="tel" name="phone" id="coPhoneInput" placeholder="Số điện thoại"
                 value="<?= e($user['phone'] ?? '') ?>" required class="co2-input"
                 pattern="[0-9]{10}" maxlength="10">
          <p id="coPhoneMsg" class="co2-field-error" hidden>Vui lòng kiểm tra số điện thoại.</p>
          <input type="email" name="email" placeholder="Email"
                 value="<?= e($user['email'] ?? '') ?>" class="co2-input">
          <div class="co2-input co2-input-static">
            <span class="co2-input-label">Quốc gia</span>
            <span>Vietnam</span>
            <input type="hidden" name="country" value="Vietnam">
          </div>
          <input type="text" name="street" placeholder="Địa chỉ, tên đường" required class="co2-input">
          <div class="co2-addr-row2">
            <select name="province_name" id="coProvince" class="co2-input co2-select" required>
              <option value="">Tỉnh / Thành phố</option>
            </select>
            <select name="ward_name" id="coWard" class="co2-input co2-select" required disabled>
              <option value="">Phường / Xã</option>
            </select>
          </div>
          <textarea name="note" placeholder="Ghi chú đơn hàng (tùy chọn)" rows="3" class="co2-input co2-textarea"></textarea>
        </div>
      </div>

      <!-- Phương thức giao hàng -->
      <div class="co2-card">
        <h2 class="co2-section-title">Phương thức giao hàng</h2>
        <div class="co2-shipping-placeholder" id="coShippingMsg">
          <span class="co2-shipping-hint">Nhập địa chỉ để xem các phương thức giao hàng</span>
        </div>
        <div class="co2-options-group" id="coShippingOption" hidden>
          <label class="co2-pay-option">
            <input type="radio" name="shipping_method" value="standard" checked>
            <span class="co2-pay-label">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
              Giao hàng tiêu chuẩn
            </span>
            <span class="co2-shipping-price">30.000đ</span>
          </label>
        </div>
      </div>

      <!-- Phương thức thanh toán -->
      <div class="co2-card">
        <h2 class="co2-section-title">Phương thức thanh toán</h2>
        <div class="co2-options-group">
          <label class="co2-pay-option">
            <input type="radio" name="payment_method" value="cod" checked>
            <span class="co2-pay-label">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
              Thanh toán khi nhận hàng (COD)
            </span>
          </label>
          <label class="co2-pay-option">
            <input type="radio" name="payment_method" value="sepay">
            <span class="co2-pay-label">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h3v3m0 4h4v-4m-4 0h4"/></svg>
              Chuyển khoản QR — MBBank
            </span>
          </label>
        </div>
      </div>

    </div><!-- /co2-main -->

    <!-- RIGHT COLUMN -->
    <div class="co2-sidebar-col">

      <!-- Giỏ hàng -->
      <div class="co2-card">
        <h2 class="co2-section-title">Giỏ hàng</h2>
        <div class="co2-items" id="coItems">
          <?php foreach ($items as $it): ?>
          <div class="co2-item" data-item-id="<?= (int) $it['cart_item_id'] ?>" data-price="<?= (int) $it['price'] ?>">
            <div class="co2-item-img">
              <?php if (!empty($it['image'])): ?>
                <img src="<?= e($it['image']) ?>" alt="<?= e($it['product_name']) ?>">
              <?php else: ?>
                <div class="co2-item-img-placeholder"></div>
              <?php endif; ?>
            </div>
            <div class="co2-item-info">
              <div class="co2-item-top">
                <div class="co2-item-name"><?= e($it['product_name']) ?></div>
                <button type="button" class="co2-item-remove" title="Xóa">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                </button>
              </div>
              <div class="co2-item-variant-badge"><?= e($it['variant_label'] ?? ('SIZE ' . ($it['size'] ?? ''))) ?></div>
              <div class="co2-item-bottom">
                <div class="co2-item-price" data-line-price><?= vnd($it['price'] * $it['quantity']) ?></div>
                <div class="co2-qty-ctrl">
                  <button type="button" class="co2-qty-btn co2-qty-minus">−</button>
                  <span class="co2-qty-num"><?= (int) $it['quantity'] ?></span>
                  <button type="button" class="co2-qty-btn co2-qty-plus">+</button>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Mã khuyến mãi -->
      <div class="co2-card">
        <h2 class="co2-section-title">Mã khuyến mãi</h2>
        <div class="co2-coupon-box">
          <div class="co2-coupon-input-wrap">
            <span class="co2-coupon-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 5H3v4a2 2 0 000 4v4h18v-4a2 2 0 000-4V5z"/><line x1="12" y1="5" x2="12" y2="19"/></svg>
            </span>
            <input type="text" id="coCouponInput" placeholder="Nhập mã khuyến mãi" class="co2-coupon-input">
            <button type="button" id="coCouponBtn" class="co2-coupon-apply-btn">Áp dụng</button>
          </div>
          <div id="coCouponMsg" class="co2-coupon-msg" hidden></div>
          <input type="hidden" name="coupon_code" id="coCouponCode">
          <input type="hidden" name="discount" id="coDiscount" value="0">
        </div>
      </div>

      <!-- Tóm tắt đơn hàng -->
      <div class="co2-card">
        <h2 class="co2-section-title">Tóm tắt đơn hàng</h2>
        <div class="co2-summary">
          <div class="co2-summary-row">
            <span>Tổng tiền hàng</span>
            <span id="coSubtotalLabel"><?= vnd($total) ?></span>
          </div>
          <div class="co2-summary-row" id="coDiscountRow" hidden>
            <span>Giảm giá</span>
            <span id="coDiscountLabel" style="color:#e33"></span>
          </div>
          <div class="co2-summary-row">
            <span>Phí vận chuyển</span>
            <span>30.000đ</span>
          </div>
          <hr class="co2-summary-divider">
          <div class="co2-summary-row co2-summary-total">
            <span>Tổng thanh toán</span>
            <span id="coTotalLabel"><?= vnd($total + 30000) ?></span>
          </div>
          <p class="co2-summary-vat">Giá trên đã bao gồm VAT</p>
        </div>
        <button type="submit" class="co2-submit-btn" style="margin-top:20px">Đặt hàng</button>
      </div>

    </div><!-- /co2-sidebar-col -->

  </form>
</div>
<script>
(function () {
  const API = 'https://provinces.open-api.vn/api/v2';
  const selP = document.getElementById('coProvince');
  const selW = document.getElementById('coWard');
  const shippingMsg = document.getElementById('coShippingMsg');
  const shippingOpt = document.getElementById('coShippingOption');

  fetch(API + '/p/')
    .then(r => r.json())
    .then(data => {
      data.forEach(p => {
        const o = document.createElement('option');
        o.value = p.name; o.dataset.code = p.code; o.textContent = p.name;
        selP.appendChild(o);
      });
    });

  selP.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    const code = opt.dataset.code;
    selW.innerHTML = '<option value="">Phường/Xã</option>';
    selW.disabled = true;
    shippingMsg.hidden = true; shippingOpt.hidden = true;
    if (!code) return;
    fetch(API + '/p/' + code + '?depth=2')
      .then(r => r.json())
      .then(data => {
        (data.wards || []).forEach(w => {
          const o = document.createElement('option');
          o.value = w.name; o.textContent = w.name;
          selW.appendChild(o);
        });
        selW.disabled = false;
        selW.blur();
      });
  });

  selW.addEventListener('change', function () {
    if (this.value) { shippingMsg.hidden = true; shippingOpt.hidden = false; }
    else            { shippingMsg.hidden = false; shippingOpt.hidden = true; }
  });
})();

// Cart qty controls in checkout sidebar
(function () {
  const CSRF = '<?= \App\Core\Csrf::token() ?>';
  let cartBase = <?= json_encode($total) ?>;

  function post(url, data) {
    return fetch(url, {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: new URLSearchParams({_csrf: CSRF, ...data}).toString(),
    }).then(r => r.json());
  }

  function refreshSummary(newBase) {
    cartBase = newBase;
    const discount = parseInt(document.getElementById('coDiscount')?.value || '0');
    const ship = 30000;
    document.getElementById('coSubtotalLabel').textContent = newBase.toLocaleString('vi-VN') + 'đ';
    const finalDiscount = Math.min(discount, newBase);
    document.getElementById('coTotalLabel').textContent = (newBase - finalDiscount + ship).toLocaleString('vi-VN') + 'đ';
  }

  document.getElementById('coItems').addEventListener('click', function (e) {
    const item = e.target.closest('.co2-item');
    if (!item) return;
    const id = item.dataset.itemId;
    const price = parseInt(item.dataset.price);
    const qtyEl = item.querySelector('.co2-qty-num');
    let qty = parseInt(qtyEl.textContent);

    if (e.target.closest('.co2-qty-minus')) {
      if (qty <= 1) return;
      qty--;
      qtyEl.textContent = qty;
      item.querySelector('[data-line-price]').textContent = (price * qty).toLocaleString('vi-VN') + 'đ';
      post('/cart/update', {cart_item_id: id, quantity: qty})
        .then(d => { if (d.ok) refreshSummary(d.total); });
    } else if (e.target.closest('.co2-qty-plus')) {
      qty++;
      qtyEl.textContent = qty;
      item.querySelector('[data-line-price]').textContent = (price * qty).toLocaleString('vi-VN') + 'đ';
      post('/cart/update', {cart_item_id: id, quantity: qty})
        .then(d => { if (d.ok) refreshSummary(d.total); });
    } else if (e.target.closest('.co2-item-remove')) {
      post('/cart/remove', {cart_item_id: id})
        .then(d => {
          if (d.ok) {
            item.remove();
            refreshSummary(d.total);
          }
        });
    }
  });
})();

// Coupon
(function () {
  const base = <?= json_encode($total) ?>;
  const ship = 30000;
  let discount = 0;

  function fmt(n) {
    return n.toLocaleString('vi-VN') + 'đ';
  }
  function updateTotal() {
    const finalDiscount = Math.min(discount, base);
    document.getElementById('coDiscount').value = finalDiscount;
    document.getElementById('coTotalLabel').textContent = fmt(base - finalDiscount + ship);
    const row = document.getElementById('coDiscountRow');
    if (finalDiscount > 0) {
      row.hidden = false;
      document.getElementById('coDiscountLabel').textContent = '-' + fmt(finalDiscount);
    } else {
      row.hidden = true;
    }
  }

  document.getElementById('coCouponBtn').addEventListener('click', function () {
    const code = document.getElementById('coCouponInput').value.trim();
    const msg  = document.getElementById('coCouponMsg');
    if (!code) return;

    this.disabled = true;
    fetch('/coupon/apply', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'code=' + encodeURIComponent(code) + '&total=' + base,
    })
    .then(r => r.json())
    .then(d => {
      msg.hidden = false;
      if (d.ok) {
        discount = d.discount;
        document.getElementById('coCouponCode').value = d.code;
        msg.className = 'co2-coupon-msg co2-coupon-ok';
        msg.textContent = '✓ ' + d.message;
        updateTotal();
      } else {
        discount = 0;
        document.getElementById('coCouponCode').value = '';
        msg.className = 'co2-coupon-msg co2-coupon-err';
        msg.textContent = d.message;
        updateTotal();
      }
    })
    .catch(() => {
      msg.hidden = false;
      msg.className = 'co2-coupon-msg co2-coupon-err';
      msg.textContent = 'Không kết nối được máy chủ.';
    })
    .finally(() => { this.disabled = false; });
  });

  document.getElementById('coCouponInput').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') { e.preventDefault(); document.getElementById('coCouponBtn').click(); }
  });

  const phoneInput = document.getElementById('coPhoneInput');
  const phoneMsg = document.getElementById('coPhoneMsg');
  function checkPhone() {
    const ok = phoneInput.checkValidity();
    phoneMsg.hidden = ok;
    return ok;
  }
  phoneInput.addEventListener('invalid', function (e) {
    e.preventDefault();
    checkPhone();
  });
  phoneInput.addEventListener('input', checkPhone);
})();
</script>
