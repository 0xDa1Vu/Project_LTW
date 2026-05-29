<?php
namespace App\Models;

use App\Core\Model;

class Review extends Model
{
    protected string $table = 'reviews';

    public function forProduct(int $productId): array
    {
        $sql = "SELECT r.*, u.name AS user_name
                FROM reviews r JOIN users u ON u.id = r.user_id
                WHERE r.product_id = :pid
                ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pid' => $productId]);
        return $stmt->fetchAll();
    }

    public function summary(int $productId): array
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) AS cnt, COALESCE(AVG(rating),0) AS avg FROM reviews WHERE product_id = :pid'
        );
        $stmt->execute(['pid' => $productId]);
        return $stmt->fetch();
    }

    /** Upsert: mỗi user 1 review / sản phẩm */
    public function upsert(int $productId, int $userId, int $rating, string $comment): void
    {
        $sql = "INSERT INTO reviews (product_id, user_id, rating, comment)
                VALUES (:pid, :uid, :rating, :comment)
                ON CONFLICT (product_id, user_id)
                DO UPDATE SET rating = EXCLUDED.rating, comment = EXCLUDED.comment, created_at = now()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'pid' => $productId, 'uid' => $userId,
            'rating' => $rating, 'comment' => $comment,
        ]);
    }

    public function allWithRefs(): array
    {
        return $this->db->query(
            "SELECT r.*, u.name AS user_name, p.name AS product_name
             FROM reviews r
             JOIN users u ON u.id = r.user_id
             JOIN products p ON p.id = r.product_id
             ORDER BY r.created_at DESC"
        )->fetchAll();
    }
}
