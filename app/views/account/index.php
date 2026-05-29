<?php use App\Core\Csrf; ?>
<section class="container section narrow">
    <h1 class="section-title">Tài khoản</h1>
    <form action="/account/profile" method="post" class="auth-card">
        <?= Csrf::field() ?>
        <label>Họ tên
            <input type="text" name="name" value="<?= e($user['name']) ?>" required>
        </label>
        <label>Email
            <input type="email" value="<?= e($user['email']) ?>" disabled>
        </label>
        <label>Số điện thoại
            <input type="text" name="phone" value="<?= e($user['phone'] ?? '') ?>">
        </label>
        <label>Địa chỉ
            <textarea name="address" rows="3"><?= e($user['address'] ?? '') ?></textarea>
        </label>
        <button type="submit" class="btn btn-dark btn-block">Lưu thay đổi</button>
    </form>
    <p style="text-align:center;margin-top:1rem"><a href="/account/orders">Xem đơn hàng của tôi →</a></p>
</section>
