<?php
namespace App\Models;

use App\Core\Model;

class Coupon extends Model
{
    protected string $table = 'coupons';

    public function findByCode(string $code): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM coupons WHERE code = :code AND is_active = true");
        $stmt->execute(['code' => strtoupper(trim($code))]);
        return $stmt->fetch() ?: null;
    }

    /** Tính số tiền được giảm (không vượt quá tổng đơn). */
    public function calcDiscount(array $coupon, int $total): int
    {
        if ($total < (int) $coupon['min_order']) return 0;
        if ($coupon['type'] === 'percent') {
            return (int) round($total * $coupon['value'] / 100);
        }
        return min((int) $coupon['value'], $total);
    }

    public function incrementUsed(int $id): void
    {
        $this->db->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE id = :id")
                 ->execute(['id' => $id]);
    }
}
