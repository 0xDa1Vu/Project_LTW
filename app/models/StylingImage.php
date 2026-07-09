<?php
namespace App\Models;

use App\Core\Model;

class StylingImage extends Model
{
    protected string $table = 'styling_images';

    public function forStyling(int $stylingId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM styling_images WHERE styling_id = :sid ORDER BY sort_order ASC, id ASC'
        );
        $stmt->execute(['sid' => $stylingId]);
        return $stmt->fetchAll();
    }

    public function cover(int $stylingId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM styling_images WHERE styling_id = :sid ORDER BY is_cover DESC, sort_order ASC, id ASC LIMIT 1'
        );
        $stmt->execute(['sid' => $stylingId]);
        return $stmt->fetch() ?: null;
    }

    /** Trả về cover của nhiều styling cùng lúc: [styling_id => image_url] */
    public function coversFor(array $stylingIds): array
    {
        if (!$stylingIds) return [];
        $in = implode(',', array_map('intval', $stylingIds));
        $rows = $this->db->query(
            "SELECT si.styling_id, si.image_url
             FROM styling_images si
             WHERE si.id = (
                 SELECT id FROM styling_images
                 WHERE styling_id = si.styling_id
                 ORDER BY is_cover DESC, sort_order ASC, id ASC
                 LIMIT 1
             )
             AND si.styling_id IN ($in)"
        )->fetchAll();
        $map = [];
        foreach ($rows as $r) { $map[(int) $r['styling_id']] = $r['image_url']; }
        return $map;
    }

    public function add(int $stylingId, string $url, bool $cover = false): int
    {
        return $this->insert([
            'styling_id' => $stylingId,
            'image_url'  => $url,
            'is_cover'   => $cover ? 1 : 0,
        ]);
    }

    public function deleteOne(int $id): ?string
    {
        $row = $this->find($id);
        if (!$row) return null;
        $this->db->prepare('DELETE FROM styling_images WHERE id = :id')->execute(['id' => $id]);
        return $row['image_url'];
    }

    public function setCover(int $id, int $stylingId): void
    {
        $this->db->prepare('UPDATE styling_images SET is_cover = false WHERE styling_id = :sid')
            ->execute(['sid' => $stylingId]);
        $this->db->prepare('UPDATE styling_images SET is_cover = true WHERE id = :id')
            ->execute(['id' => $id]);
    }

    public function reorder(array $ids): void
    {
        $stmt = $this->db->prepare('UPDATE styling_images SET sort_order = :ord WHERE id = :id');
        foreach ($ids as $order => $id) {
            $stmt->execute(['ord' => $order, 'id' => (int) $id]);
        }
    }
}
