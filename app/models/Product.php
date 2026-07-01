<?php
namespace App\Models;

use App\Core\Model;

class Product extends Model
{
    protected string $table = 'products';

    /** Ảnh chính của sản phẩm (sub-query tiện cho danh sách) */
    private const PRIMARY_IMAGE_SQL =
        "(SELECT image_url FROM product_images pi
          WHERE pi.product_id = p.id
          ORDER BY is_primary DESC, id ASC LIMIT 1) AS image";

    /**
     * Danh sách sản phẩm cho trang khách với lọc/sắp xếp/phân trang.
     * $f: category_id, q (search), min_price, max_price, size, color, sort, page, per_page
     * Trả ['items'=>[], 'total'=>int, 'page'=>int, 'pages'=>int]
     */
    public function browse(array $f): array
    {
        $where  = ["p.status = 'active'"];
        $params = [];

        if (!empty($f['category_id'])) {
            $where[] = 'p.category_id = :cat';
            $params['cat'] = (int) $f['category_id'];
        }
        if (!empty($f['q'])) {
            $where[] = '(p.name ILIKE :q OR p.brand ILIKE :q)';
            $params['q'] = '%' . $f['q'] . '%';
        }
        if (isset($f['min_price']) && $f['min_price'] !== '') {
            $where[] = 'COALESCE(p.sale_price, p.price) >= :minp';
            $params['minp'] = (float) $f['min_price'];
        }
        if (isset($f['max_price']) && $f['max_price'] !== '') {
            $where[] = 'COALESCE(p.sale_price, p.price) <= :maxp';
            $params['maxp'] = (float) $f['max_price'];
        }
        if (!empty($f['size'])) {
            $sizes = (array) $f['size'];
            $ph = [];
            foreach (array_values($sizes) as $i => $v) {
                $ph[] = ":size$i";
                $params["size$i"] = $v;
            }
            $where[] = 'EXISTS (SELECT 1 FROM variants v WHERE v.product_id = p.id AND v.size IN (' . implode(',', $ph) . '))';
        }
        if (!empty($f['color'])) {
            $colors = (array) $f['color'];
            $ph = [];
            foreach (array_values($colors) as $i => $v) {
                $ph[] = ":color$i";
                $params["color$i"] = $v;
            }
            $where[] = 'EXISTS (SELECT 1 FROM variants v WHERE v.product_id = p.id AND v.color IN (' . implode(',', $ph) . '))';
        }

        $whereSql = implode(' AND ', $where);

        $order = match ($f['sort'] ?? '') {
            'price_asc'  => 'COALESCE(p.sale_price, p.price) ASC',
            'price_desc' => 'COALESCE(p.sale_price, p.price) DESC',
            'name'       => 'p.name ASC',
            default      => 'p.created_at DESC',
        };

        // Đếm tổng
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM products p WHERE $whereSql");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $perPage = max(1, (int) ($f['per_page'] ?? 12));
        $page    = max(1, (int) ($f['page'] ?? 1));
        $offset  = ($page - 1) * $perPage;

        $sql = "SELECT p.*, " . self::PRIMARY_IMAGE_SQL . "
                FROM products p
                WHERE $whereSql
                ORDER BY $order
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue(":$k", $v);
        }
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'page'  => $page,
            'pages' => (int) ceil($total / $perPage),
        ];
    }

    public function featured(int $limit = 8): array
    {
        $sql = "SELECT p.*, " . self::PRIMARY_IMAGE_SQL . "
                FROM products p WHERE p.status = 'active' AND p.is_featured = true
                ORDER BY p.created_at DESC LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function bestSellers(int $limit = 8): array
    {
        $sql = "SELECT p.*, " . self::PRIMARY_IMAGE_SQL . ",
                       COALESCE(SUM(oi.quantity), 0) AS sold
                FROM products p
                JOIN variants v ON v.product_id = p.id
                LEFT JOIN order_items oi ON oi.variant_id = v.id
                WHERE p.status = 'active'
                GROUP BY p.id
                ORDER BY sold DESC, p.created_at DESC
                LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    public function withCategory(int $id): ?array
    {
        $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $d): int
    {
        return $this->insert([
            'category_id' => $d['category_id'] ?: null,
            'name'        => $d['name'],
            'slug'        => $d['slug'],
            'description' => $d['description'] ?? null,
            'price'       => $d['price'],
            'sale_price'  => $d['sale_price'] !== '' ? $d['sale_price'] : null,
            'brand'       => $d['brand'] ?? null,
            'status'      => $d['status'] ?? 'active',
        ]);
    }

    public function modify(int $id, array $d): bool
    {
        return $this->update($id, [
            'category_id' => $d['category_id'] ?: null,
            'name'        => $d['name'],
            'slug'        => $d['slug'],
            'description' => $d['description'] ?? null,
            'price'       => $d['price'],
            'sale_price'  => $d['sale_price'] !== '' ? $d['sale_price'] : null,
            'brand'       => $d['brand'] ?? null,
            'status'      => $d['status'] ?? 'active',
            'is_featured' => ($d['is_featured'] ?? false) ? 't' : 'f',
        ]);
    }

    /** ID sản phẩm bán chạy nhất (dùng để gắn badge BEST SELLER) */
    public function bestSellerIds(int $limit = 8): array
    {
        $sql = "SELECT p.id
                FROM products p
                JOIN variants v ON v.product_id = p.id
                LEFT JOIN order_items oi ON oi.variant_id = v.id
                WHERE p.status = 'active'
                GROUP BY p.id
                HAVING COALESCE(SUM(oi.quantity), 0) > 0
                ORDER BY COALESCE(SUM(oi.quantity), 0) DESC
                LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    /** Danh sách size/color phân biệt để dựng bộ lọc */
    public function distinctSizes(): array
    {
        return $this->db->query("SELECT DISTINCT size FROM variants ORDER BY size")
                        ->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function distinctColors(): array
    {
        return $this->db->query("SELECT DISTINCT color FROM variants ORDER BY color")
                        ->fetchAll(\PDO::FETCH_COLUMN);
    }
}
