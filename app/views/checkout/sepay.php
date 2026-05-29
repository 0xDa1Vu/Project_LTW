<section class="container section narrow">
    <div class="sepay-box" data-order-id="<?= (int) $order['id'] ?>">
        <h1 class="section-title">Quét mã để thanh toán</h1>
        <p class="sepay-sub">Đơn hàng <strong>#<?= (int) $order['id'] ?></strong> &middot; Số tiền <strong><?= vnd($amount) ?></strong></p>

        <div class="sepay-grid">
            <div class="sepay-qr">
                <img src="<?= e($qrUrl) ?>" alt="Mã QR chuyển khoản đơn #<?= (int) $order['id'] ?>" width="280" height="360">
            </div>

            <div class="sepay-info">
                <h3>Thông tin chuyển khoản</h3>
                <dl class="sepay-dl">
                    <dt>Ngân hàng</dt><dd><?= e($bank) ?></dd>
                    <dt>Số tài khoản</dt><dd><strong><?= e($account) ?></strong></dd>
                    <dt>Chủ tài khoản</dt><dd><?= e($accountName) ?></dd>
                    <dt>Số tiền</dt><dd><strong><?= vnd($amount) ?></strong></dd>
                    <dt>Nội dung CK</dt><dd><strong class="sepay-content"><?= e($content) ?></strong></dd>
                </dl>
                <p class="sepay-note">
                    Vui lòng chuyển khoản <strong>đúng số tiền</strong> và <strong>giữ nguyên nội dung</strong>
                    <code><?= e($content) ?></code> để hệ thống tự động xác nhận.
                </p>

                <div class="sepay-status" id="sepayStatus" hidden></div>
                <button type="button" class="btn btn-dark btn-block" id="sepayCheckBtn">
                    Tôi đã chuyển khoản — Kiểm tra
                </button>
                <a href="/order/success/<?= (int) $order['id'] ?>" class="btn btn-outline btn-block">
                    Để sau, xem đơn hàng
                </a>
            </div>
        </div>
    </div>
</section>

<script src="/js/sepay.js"></script>
