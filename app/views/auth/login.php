<?php use App\Core\Csrf; ?>
<section class="container section narrow">
    <div class="auth-card">
        <h1>Đăng nhập</h1>
        <form action="/login" method="post" data-validate>
            <?= Csrf::field() ?>
            <label>Email
                <input type="email" name="email" value="<?= e($email ?? '') ?>" required>
            </label>
            <label>Mật khẩu
                <input type="password" name="password" required>
            </label>
            <button type="submit" class="btn btn-dark btn-block">Đăng nhập</button>
        </form>
        <p class="auth-alt">Chưa có tài khoản? <a href="/register">Đăng ký</a></p>
    </div>
</section>
