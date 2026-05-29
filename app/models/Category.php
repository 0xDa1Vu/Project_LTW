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
}
