<?php use App\Core\Auth; use App\Core\Csrf; ?>
<div class="co2-wrap">
  <form action="/checkout" method="post" class="co2-layout">
    <?= Csrf::field() ?>

    <!-- LEFT COLUMN -->
    <div class="co2-main">

      <?php if (!Auth::id()): ?>
      <div class="co2-login-banner">
        Đăng nhập để mua hàng tiện lợi và nhận nhiều ưu đãi hơn nữa
        <a href="/login" class="co2-login-btn">Đăng nhập</a>
      </div>
      <?php endif; ?>

      <!-- Thông tin giao hàng -->
      <div class="co2-section">
        <h2 class="co2-section-title">Thông tin giao hàng</h2>
        <div class="co2-field-group">
          <input type="text" name="name" placeholder="Nhập họ và tên"
                 value="<?= e($user['name'] ?? '') ?>" required class="co2-input">
          <input type="tel" name="phone" placeholder="Nhập số điện thoại"
                 value="<?= e($user['phone'] ?? '') ?>" required class="co2-input">
          <input type="text" name="address" placeholder="Địa chỉ, tên đường"
                 value="<?= e($user['address'] ?? '') ?>" required class="co2-input">
        </div>
      </div>

      <!-- Phương thức thanh toán -->
      <div class="co2-section">
        <h2 class="co2-section-title">Phương thức thanh toán</h2>
        <label class="co2-pay-option">
          <input type="radio" name="payment_method" value="cod" checked>
          <span class="co2-pay-label">
            <span class="co2-pay-icon">🚚</span>
            Thanh toán khi nhận hàng (COD)
          </span>
        </label>
        <label class="co2-pay-option">
          <input type="radio" name="payment_method" value="sepay">
          <span class="co2-pay-label">
            <span class="co2-pay-icon">🏦</span>
            Chuyển khoản qua QR - MB
          </span>
        </label>
      </div>

      <!-- Ghi chú -->
      <div class="co2-section">
        <h2 class="co2-section-title">Ghi chú đơn hàng</h2>
        <textarea name="note" placeholder="Ghi chú đơn hàng" rows="3" class="co2-input co2-textarea"></textarea>
      </div>

    </div><!-- /co2-main -->

    <!-- RIGHT COLUMN -->
    <aside class="co2-sidebar">
      <h2 class="co2-section-title">Giỏ hàng</h2>

      <div class="co2-items">
        <?php foreach ($items as $it): ?>
        <div class="co2-item">
          <div class="co2-item-img">
            <?php if (!empty($it['image'])): ?>
              <img src="<?= e($it['image']) ?>" alt="<?= e($it['product_name']) ?>">
            <?php else: ?>
              <div class="co2-item-img-placeholder"></div>
            <?php endif; ?>
            <span class="co2-item-qty"><?= (int) $it['quantity'] ?></span>
          </div>
          <div class="co2-item-info">
            <div class="co2-item-name"><?= e($it['product_name']) ?></div>
            <div class="co2-item-variant">SIZE <?= e($it['size']) ?></div>
          </div>
          <div class="co2-item-price"><?= vnd($it['price'] * $it['quantity']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="co2-summary">
        <div class="co2-summary-row">
          <span>Tổng tiền hàng</span>
          <span><?= vnd($total) ?></span>
        </div>
        <div class="co2-summary-row">
          <span>Phí vận chuyển</span>
          <span>30.000đ</span>
        </div>
        <div class="co2-summary-row co2-summary-total">
          <span>Tổng thanh toán</span>
          <span><?= vnd($total + 30000) ?></span>
        </div>
      </div>

      <button type="submit" class="co2-submit-btn">Đặt hàng</button>
    </aside>

  </form>
</div>
