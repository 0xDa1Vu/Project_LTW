<?php
namespace App\Models;

use App\Core\Model;

class Variant extends Model
{
    protected string $table = 'variants';

    public function forProduct(int $productId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM variants WHERE product_id = :pid ORDER BY size, color'
        );
        $stmt->execute(['pid' => $productId]);
        return $stmt->fetchAll();
    }

    /** Variant kèm thông tin sản phẩm (dùng cho giỏ hàng) */
    public function withProduct(int $variantId): ?array
    {
        $sql = "SELECT v.*, p.name AS product_name, p.slug AS product_slug,
                       p.price, p.sale_price,
                       (SELECT image_url FROM product_images pi
                        WHERE pi.product_id = p.id
                        ORDER BY is_primary DESC, id ASC LIMIT 1) AS image
                FROM variants v
                JOIN products p ON p.id = v.product_id
                WHERE v.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $variantId]);
        return $stmt->fetch() ?: null;
    }

    public function add(int $productId, array $d): int
    {
        return $this->insert([
            'product_id' => $productId,
            'size'       => $d['size'],
            'color'      => $d['color'],
            'stock'      => (int) $d['stock'],
            'sku'        => $d['sku'] ?: null,
        ]);
    }

    public function deleteForProduct(int $productId): void
    {
        $stmt = $this->db->prepare('DELETE FROM variants WHERE product_id = :pid');
        $stmt->execute(['pid' => $productId]);
    }

    /** Trừ kho an toàn (chỉ trừ nếu còn đủ); trả về true nếu thành công */
    public function decreaseStock(int $variantId, int $qty): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE variants SET stock = stock - :q1
             WHERE id = :id AND stock >= :q2'
        );
        $stmt->execute(['q1' => $qty, 'q2' => $qty, 'id' => $variantId]);
        return $stmt->rowCount() > 0;
    }
}
