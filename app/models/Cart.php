<?php
namespace App\Models;

use App\Core\Model;

/**
 * Giỏ hàng: gắn theo user_id (đã đăng nhập) hoặc session_id (khách).
 */
class Cart extends Model
{
    protected string $table = 'carts';

    /** Lấy (hoặc tạo) giỏ hiện tại cho user/session */
    public function current(?int $userId, string $sessionId): array
    {
        if ($userId) {
            $stmt = $this->db->prepare('SELECT * FROM carts WHERE user_id = :uid ORDER BY id DESC LIMIT 1');
            $stmt->execute(['uid' => $userId]);
        } else {
            $stmt = $this->db->prepare('SELECT * FROM carts WHERE session_id = :sid AND user_id IS NULL ORDER BY id DESC LIMIT 1');
            $stmt->execute(['sid' => $sessionId]);
        }
        $cart = $stmt->fetch();
        if ($cart) {
            return $cart;
        }
        $id = $this->insert([
            'user_id'    => $userId,
            'session_id' => $userId ? null : $sessionId,
        ]);
        return $this->find($id);
    }

    /** Các item trong giỏ kèm thông tin variant + sản phẩm + giá hiệu lực */
    public function items(int $cartId): array
    {
        $sql = "SELECT ci.id AS cart_item_id, ci.quantity, ci.variant_id,
                       v.size, v.color, v.stock,
                       p.id AS product_id, p.name AS product_name, p.slug,
                       COALESCE(p.sale_price, p.price) AS price,
                       (SELECT image_url FROM product_images pi
                        WHERE pi.product_id = p.id
                        ORDER BY is_primary DESC, id ASC LIMIT 1) AS image
                FROM cart_items ci
                JOIN variants v ON v.id = ci.variant_id
                JOIN products p ON p.id = v.product_id
                WHERE ci.cart_id = :cid
                ORDER BY ci.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cid' => $cartId]);
        return $stmt->fetchAll();
    }

    public function addItem(int $cartId, int $variantId, int $qty): void
    {
        // Upsert: nếu đã có variant trong giỏ thì cộng dồn
        $sql = "INSERT INTO cart_items (cart_id, variant_id, quantity)
                VALUES (:cid, :vid, :q)
                ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cid' => $cartId, 'vid' => $variantId, 'q' => $qty]);
    }

    public function updateItem(int $cartItemId, int $qty): void
    {
        $stmt = $this->db->prepare('UPDATE cart_items SET quantity = :q WHERE id = :id');
        $stmt->execute(['q' => max(1, $qty), 'id' => $cartItemId]);
    }

    public function removeItem(int $cartItemId): void
    {
        $stmt = $this->db->prepare('DELETE FROM cart_items WHERE id = :id');
        $stmt->execute(['id' => $cartItemId]);
    }

    public function clear(int $cartId): void
    {
        $stmt = $this->db->prepare('DELETE FROM cart_items WHERE cart_id = :cid');
        $stmt->execute(['cid' => $cartId]);
    }

    public function count(int $cartId): int
    {
        $stmt = $this->db->prepare('SELECT COALESCE(SUM(quantity),0) FROM cart_items WHERE cart_id = :cid');
        $stmt->execute(['cid' => $cartId]);
        return (int) $stmt->fetchColumn();
    }

    /** Gộp giỏ khách (session) vào giỏ user khi đăng nhập */
    public function mergeSessionToUser(string $sessionId, int $userId): void
    {
        $guest = $this->db->prepare('SELECT id FROM carts WHERE session_id = :sid AND user_id IS NULL');
        $guest->execute(['sid' => $sessionId]);
        $guestCartId = $guest->fetchColumn();
        if (!$guestCartId) {
            return;
        }
        $userCart = $this->current($userId, $sessionId);
        $items = $this->items((int) $guestCartId);
        foreach ($items as $it) {
            $this->addItem((int) $userCart['id'], (int) $it['variant_id'], (int) $it['quantity']);
        }
        $this->delete((int) $guestCartId);
    }
}
