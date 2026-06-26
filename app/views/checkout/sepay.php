<div class="sp2-wrap" id="sepayWrap" data-order-id="<?= (int) $order['id'] ?>">
  <div class="sp2-layout">

    <!-- LEFT -->
    <div class="sp2-main">

      <!-- Card 1: Status -->
      <div class="sp2-card sp2-status-card">
        <div class="sp2-status-top">
          <div class="sp2-status-icon-wrap">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#b8860b" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          </div>
          <div class="sp2-status-texts">
            <div class="sp2-status-title">Chờ thanh toán</div>
            <div class="sp2-status-sub">Đơn hàng &nbsp;<strong>#<?= (int) $order['id'] ?></strong>
              <button type="button" class="sp2-copy-btn" data-copy="<?= (int) $order['id'] ?>">⧉</button>
            </div>
          </div>
        </div>
        <div class="sp2-status-steps">
          <span class="sp2-step sp2-step-done">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M8 12l3 3 5-5"/></svg>
            Đặt hàng thành công
          </span>
          <span class="sp2-step-time">vài giây trước</span>
        </div>
      </div>

      <!-- Card 2: Thanh toán -->
      <div class="sp2-card">
        <div class="sp2-pay-badge-wrap">
          <span class="sp2-pay-badge sp2-badge-pending" id="sp2Badge">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            Chờ thanh toán
          </span>
        </div>

        <div class="sp2-pay-rows">
          <div class="sp2-pay-row"><span>Tổng đơn</span><span><?= vnd($amount) ?></span></div>
          <div class="sp2-pay-row"><span>Cần thanh toán</span><span class="sp2-bold"><?= vnd($amount) ?></span></div>
          <div class="sp2-pay-row"><span>Phương thức</span><span>Chuyển khoản qua QR - <?= e($bank) ?></span></div>
        </div>

        <button type="button" class="sp2-btn-primary" id="sp2ModalBtn">Thanh toán</button>

        <!-- Nội dung CK + QR to -->
        <div class="sp2-ck-box">
          <div class="sp2-ck-left">
            <div class="sp2-ck-title">Nội dung chuyển khoản</div>
            <div class="sp2-ck-rows">
              <div class="sp2-ck-row"><span class="sp2-ck-k">Tài khoản</span><span class="sp2-ck-v"><?= e($accountName) ?></span></div>
              <div class="sp2-ck-row"><span class="sp2-ck-k">Ngân hàng</span><span class="sp2-ck-v"><?= e($bank) ?></span></div>
              <div class="sp2-ck-row">
                <span class="sp2-ck-k">Số tài khoản</span>
                <span class="sp2-ck-v"><?= e($account) ?> <button type="button" class="sp2-copy-btn" data-copy="<?= e($account) ?>">⧉</button></span>
              </div>
              <div class="sp2-ck-row">
                <span class="sp2-ck-k">Nội dung</span>
                <span class="sp2-ck-v"><?= e($content) ?> <button type="button" class="sp2-copy-btn" data-copy="<?= e($content) ?>">⧉</button></span>
              </div>
              <div class="sp2-ck-row">
                <span class="sp2-ck-k">Số tiền</span>
                <span class="sp2-ck-v"><?= number_format($amount, 0, ',', '.') ?>đ <button type="button" class="sp2-copy-btn" data-copy="<?= $amount ?>">⧉</button></span>
              </div>
            </div>
          </div>
          <div class="sp2-ck-right">
            <img src="<?= e($qrUrl) ?>" alt="QR chuyển khoản" class="sp2-qr-img">
          </div>
        </div>

        <div class="sp2-status-msg" id="sepayStatus" hidden></div>
      </div>

      <!-- Card 3: Trạng thái + địa chỉ -->
      <div class="sp2-card sp2-addr-card">
        <div class="sp2-addr-badge">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          Đang chuẩn bị hàng
        </div>
        <div class="sp2-addr-city"><?= e($order['shipping_address']) ?></div>
      </div>

      <!-- Card 4: Địa chỉ nhận hàng -->
      <div class="sp2-card">
        <div class="sp2-addr-title">Địa chỉ nhận hàng</div>
        <div class="sp2-addr-row">
          <span><?= e($order['customer_name']) ?></span>
          <span class="sp2-dot">·</span>
          <span><?= e($order['phone']) ?></span>
        </div>
        <div class="sp2-addr-line"><?= e($order['shipping_address']) ?></div>
      </div>

    </div><!-- /sp2-main -->

    <!-- RIGHT: sidebar -->
    <aside class="sp2-sidebar">
      <h2 class="sp2-sidebar-title">Giỏ hàng</h2>

      <div class="sp2-items">
        <?php foreach ($items as $it): ?>
        <div class="sp2-item">
          <div class="sp2-item-img-wrap">
            <?php if (!empty($it['image'])): ?>
              <img src="<?= e($it['image']) ?>" alt="<?= e($it['product_name']) ?>" class="sp2-item-img">
            <?php else: ?>
              <div class="sp2-item-img sp2-img-placeholder"></div>
            <?php endif; ?>
            <span class="sp2-item-badge"><?= (int) $it['quantity'] ?></span>
          </div>
          <div class="sp2-item-info">
            <div class="sp2-item-name"><?= e($it['product_name']) ?></div>
            <div class="sp2-item-meta">SIZE <?= e($it['size']) ?></div>
          </div>
          <div class="sp2-item-price"><?= vnd($it['price'] * $it['quantity']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="sp2-divider"></div>

      <div class="sp2-sum-rows">
        <div class="sp2-sum-row"><span>Tổng tiền hàng</span><span><?= vnd($order['total'] - 30000) ?></span></div>
        <div class="sp2-sum-row"><span>Phí vận chuyển</span><span>30.000đ</span></div>
        <div class="sp2-sum-row sp2-sum-total">
          <span>Tổng thanh toán</span>
          <span><?= vnd($order['total']) ?></span>
        </div>
        <div class="sp2-sum-vat">Giá trên đã bao gồm VAT</div>
      </div>
    </aside>

  </div>
</div>

<!-- Modal đổi phương thức -->
<div class="sp2-modal-overlay" id="sp2Modal" hidden>
  <div class="sp2-modal">
    <div class="sp2-modal-header">
      <span class="sp2-modal-title">Đổi phương thức</span>
      <button type="button" class="sp2-modal-close" id="sp2ModalClose">✕</button>
    </div>
    <div class="sp2-modal-body">
      <label class="sp2-modal-option sp2-modal-option-active">
        <input type="radio" name="modal_method" value="sepay" checked>
        <span class="sp2-modal-opt-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h3v3m0 4h4v-4m-4 0h4"/></svg>
        </span>
        <span>Chuyển khoản qua QR - MB</span>
      </label>
    </div>
    <button type="button" class="sp2-modal-confirm" id="sp2ModalConfirm">Xác nhận</button>
  </div>
</div>

<script src="/js/sepay.js"></script>
<script>
document.querySelectorAll('.sp2-copy-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    navigator.clipboard.writeText(btn.dataset.copy).then(() => {
      const orig = btn.textContent;
      btn.textContent = '✓';
      setTimeout(() => btn.textContent = orig, 1500);
    });
  });
});

// Modal
const modal   = document.getElementById('sp2Modal');
const openBtn = document.getElementById('sp2ModalBtn');
const closeBtn= document.getElementById('sp2ModalClose');
const confirm = document.getElementById('sp2ModalConfirm');
if (openBtn)  openBtn.addEventListener('click', () => { modal.hidden = false; });
if (closeBtn) closeBtn.addEventListener('click', () => { modal.hidden = true; });
if (confirm)  confirm.addEventListener('click', () => { modal.hidden = true; });
if (modal)    modal.addEventListener('click', e => { if (e.target === modal) modal.hidden = true; });
</script>
