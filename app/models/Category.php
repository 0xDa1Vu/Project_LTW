<?php
namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    protected string $table = 'categories';

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $d): int
    {
        return $this->insert([
            'name'      => $d['name'],
            'slug'      => $d['slug'],
            'parent_id' => $d['parent_id'] ?: null,
        ]);
    }

    public function modify(int $id, array $d): bool
    {
        return $this->update($id, [
            'name'      => $d['name'],
            'slug'      => $d['slug'],
            'parent_id' => $d['parent_id'] ?: null,
        ]);
    }

    public function allOrdered(): array
    {
        return $this->db->query('SELECT * FROM categories ORDER BY name')->fetchAll();
    }

    /** Trả về mảng danh mục cha, mỗi cha có key 'children' chứa các con */
    public function allGrouped(): array
    {
        $rows = $this->db->query(
            'SELECT * FROM categories ORDER BY parent_id, name'
        )->fetchAll();

        $parents = [];
        $children = [];
        foreach ($rows as $row) {
            if ($row['parent_id'] === null) {
                $row['children'] = [];
                $parents[$row['id']] = $row;
            } else {
                $children[(int)$row['parent_id']][] = $row;
            }
        }
        foreach ($children as $pid => $kids) {
            if (isset($parents[$pid])) {
                $parents[$pid]['children'] = $kids;
            }
        }
        return array_values($parents);
    }
}
