<?php
namespace App\Models;

use App\Core\Model;

class ProductImage extends Model
{
    protected string $table = 'product_images';

    public function forProduct(int $productId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM product_images WHERE product_id = :pid ORDER BY is_primary DESC, id ASC'
        );
        $stmt->execute(['pid' => $productId]);
        return $stmt->fetchAll();
    }

    public function add(int $productId, string $url, bool $primary = false): int
    {
        return $this->insert([
            'product_id' => $productId,
            'image_url'  => $url,
            'is_primary' => $primary ? 'true' : 'false',
        ]);
    }

    public function deleteForProduct(int $productId): void
    {
        $stmt = $this->db->prepare('DELETE FROM product_images WHERE product_id = :pid');
        $stmt->execute(['pid' => $productId]);
    }
}
