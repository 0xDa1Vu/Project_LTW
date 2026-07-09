<?php
namespace App\Models;

use App\Core\Model;

class ProductImage extends Model
{
    protected string $table = 'product_images';

    public function forProduct(int $productId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM product_images WHERE product_id = :pid ORDER BY sort_order ASC, id ASC'
        );
        $stmt->execute(['pid' => $productId]);
        return $stmt->fetchAll();
    }

    public function add(int $productId, string $url, bool $primary = false): int
    {
        return $this->insert([
            'product_id' => $productId,
            'image_url'  => $url,
            'is_primary' => $primary ? 1 : 0,
        ]);
    }

    public function deleteForProduct(int $productId): void
    {
        $stmt = $this->db->prepare('DELETE FROM product_images WHERE product_id = :pid');
        $stmt->execute(['pid' => $productId]);
    }

    public function deleteOne(int $id): ?string
    {
        $row = $this->find($id);
        if (!$row) return null;
        $this->db->prepare('DELETE FROM product_images WHERE id = :id')->execute(['id' => $id]);
        return $row['image_url'];
    }

    public function reorder(array $ids): void
    {
        $stmt = $this->db->prepare('UPDATE product_images SET sort_order = :ord WHERE id = :id');
        foreach ($ids as $order => $id) {
            $stmt->execute(['ord' => $order, 'id' => (int) $id]);
        }
    }

    public function setPrimary(int $id, int $productId): void
    {
        $this->db->prepare('UPDATE product_images SET is_primary = false WHERE product_id = :pid')
            ->execute(['pid' => $productId]);
        $this->db->prepare('UPDATE product_images SET is_primary = true WHERE id = :id')
            ->execute(['id' => $id]);
    }
}
