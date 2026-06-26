<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Coupon;

class CouponController extends Controller
{
    public function apply(): void
    {
        $code  = trim($_POST['code'] ?? '');
        $total = (int) ($_POST['total'] ?? 0);

        if ($code === '') {
            $this->json(['ok' => false, 'message' => 'Vui lòng nhập mã.'], 400);
            return;
        }

        $model  = new Coupon();
        $coupon = $model->findByCode($code);

        if (!$coupon) {
            $this->json(['ok' => false, 'message' => 'Mã không hợp lệ hoặc đã hết hạn.'], 422);
            return;
        }
        if ($coupon['max_uses'] !== null && $coupon['used_count'] >= $coupon['max_uses']) {
            $this->json(['ok' => false, 'message' => 'Mã đã hết lượt sử dụng.'], 422);
            return;
        }
        if ($coupon['expires_at'] && strtotime($coupon['expires_at']) < time()) {
            $this->json(['ok' => false, 'message' => 'Mã đã hết hạn.'], 422);
            return;
        }
        if ($total < (int) $coupon['min_order']) {
            $this->json(['ok' => false, 'message' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($coupon['min_order'], 0, ',', '.') . 'đ.'], 422);
            return;
        }

        $discount = $model->calcDiscount($coupon, $total);
        $this->json([
            'ok'       => true,
            'code'     => strtoupper($code),
            'discount' => $discount,
            'message'  => $coupon['type'] === 'percent'
                ? 'Giảm ' . (int)$coupon['value'] . '%'
                : 'Giảm ' . number_format($coupon['value'], 0, ',', '.') . 'đ',
        ]);
    }
}
