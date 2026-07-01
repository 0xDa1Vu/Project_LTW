<?php
namespace App\Models;

use App\Core\Model;

class Styling extends Model
{
    protected string $table = 'stylings';

    public function allOrdered(): array
    {
        return $this->db->query('SELECT * FROM stylings ORDER BY sort_order, id')->fetchAll();
    }

    public function create(array $d): int
    {
        return $this->insert([
            'title'      => $d['title'],
            'model_info' => $d['model_info'] ?? null,
            'sort_order' => (int) ($d['sort_order'] ?? 0),
        ]);
    }

    public function modify(int $id, array $d): bool
    {
        return $this->update($id, [
            'title'      => $d['title'],
            'model_info' => $d['model_info'] ?? null,
            'sort_order' => (int) ($d['sort_order'] ?? 0),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
