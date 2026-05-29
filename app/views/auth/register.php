<?php use App\Core\Csrf; $old = $old ?? []; $errors = $errors ?? []; ?>
<section class="container section narrow">
    <div class="auth-card">
        <h1>Đăng ký</h1>
        <form action="/register" method="post" data-validate>
            <?= Csrf::field() ?>
            <label>Họ tên
                <input type="text" name="name" value="<?= e($old['name'] ?? '') ?>" required>
                <?php if (!empty($errors['name'])): ?><span class="field-error"><?= e($errors['name'][0]) ?></span><?php endif; ?>
            </label>
            <label>Email
                <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" required>
                <?php if (!empty($errors['email'])): ?><span class="field-error"><?= e($errors['email'][0]) ?></span><?php endif; ?>
            </label>
            <label>Số điện thoại
                <input type="text" name="phone" value="<?= e($old['phone'] ?? '') ?>">
            </label>
            <label>Mật khẩu
                <input type="password" name="password" required minlength="6">
                <?php if (!empty($errors['password'])): ?><span class="field-error"><?= e($errors['password'][0]) ?></span><?php endif; ?>
            </label>
            <label>Xác nhận mật khẩu
                <input type="password" name="password_confirmation" required>
            </label>
            <button type="submit" class="btn btn-dark btn-block">Đăng ký</button>
        </form>
        <p class="auth-alt">Đã có tài khoản? <a href="/login">Đăng nhập</a></p>
    </div>
</section>
