<?php
namespace App\Models;

use App\Core\Model;

class Order extends Model
{
    protected string $table = 'orders';

    /**
     * Tạo đơn hàng trong transaction: insert order + order_items + trừ kho.
     * $items: mảng từ Cart::items(). Trả về order id, hoặc ném Exception nếu hết kho.
     */
    public function placeOrder(array $info, array $items): int
    {
        $variantModel = new Variant();
        $this->db->beginTransaction();
        try {
            $total = 0;
            foreach ($items as $it) {
                $total += $it['price'] * $it['quantity'];
            }

            $orderId = $this->insert([
                'user_id'          => $info['user_id'],
                'total'            => $total,
                'status'           => 'pending',
                'payment_method'   => $info['payment_method'],
                'payment_status'   => 'unpaid',
                'shipping_address' => $info['shipping_address'],
                'phone'            => $info['phone'],
                'customer_name'    => $info['customer_name'],
                'note'             => $info['note'] ?? null,
            ]);

            $itemStmt = $this->db->prepare(
                "INSERT INTO order_items (order_id, variant_id, product_name, variant_label, price, quantity)
                 VALUES (:oid, :vid, :pname, :label, :price, :qty)"
            );

            foreach ($items as $it) {
                // Trừ kho an toàn; nếu không đủ -> rollback
                if (!$variantModel->decreaseStock((int) $it['variant_id'], (int) $it['quantity'])) {
                    throw new \RuntimeException("Sản phẩm \"{$it['product_name']}\" ({$it['size']}/{$it['color']}) không đủ hàng.");
                }
                $itemStmt->execute([
                    'oid'   => $orderId,
                    'vid'   => $it['variant_id'],
                    'pname' => $it['product_name'],
                    'label' => $it['size'] . ' / ' . $it['color'],
                    'price' => $it['price'],
                    'qty'   => $it['quantity'],
                ]);
            }

            $this->db->commit();
            return $orderId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function items(int $orderId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM order_items WHERE order_id = :oid');
        $stmt->execute(['oid' => $orderId]);
        return $stmt->fetchAll();
    }

    public function forUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC');
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    public function findForUser(int $orderId, int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = :id AND user_id = :uid');
        $stmt->execute(['id' => $orderId, 'uid' => $userId]);
        return $stmt->fetch() ?: null;
    }

    public function allWithUser(): array
    {
        return $this->db->query(
            "SELECT o.*, u.name AS user_name, u.email
             FROM orders o LEFT JOIN users u ON u.id = o.user_id
             ORDER BY o.created_at DESC"
        )->fetchAll();
    }

    public function setStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }

    public function setPayment(int $id, string $paymentStatus, ?string $newStatus = null): bool
    {
        $data = ['payment_status' => $paymentStatus];
        if ($newStatus) {
            $data['status'] = $newStatus;
        }
        return $this->update($id, $data);
    }
}
