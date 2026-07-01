<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\Styling;
use App\Models\StylingImage;

class StylingController extends Controller
{
    public function index(): void
    {
        Auth::requireRole('admin');
        $stylings = (new Styling())->allOrdered();
        $ids = array_column($stylings, 'id');
        $covers = (new StylingImage())->coversFor($ids);
        $this->view('admin/stylings/index', [
            'title'    => 'Quản lý Styling',
            'stylings' => $stylings,
            'covers'   => $covers,
        ], 'admin');
    }

    public function create(): void
    {
        Auth::requireRole('admin');
        $this->view('admin/stylings/form', [
            'title'   => 'Thêm Styling',
            'styling' => null,
            'images'  => [],
        ], 'admin');
    }

    public function store(): void
    {
        Auth::requireRole('admin');
        Csrf::verify();
        $title = $this->input('title');
        if ($title === '') {
            Session::flash('error', 'Tiêu đề là bắt buộc.');
            $this->redirect('/admin/stylings');
        }
        $id = (new Styling())->create([
            'title'      => $title,
            'model_info' => $this->input('model_info'),
            'sort_order' => $this->input('sort_order') ?: 0,
        ]);
        $this->saveImages($id);
        Session::flash('success', 'Đã thêm styling.');
        $this->redirect('/admin/stylings');
    }

    public function edit(string $id): void
    {
        Auth::requireRole('admin');
        $styling = (new Styling())->find((int) $id);
        if (!$styling) { $this->redirect('/admin/stylings'); }
        $this->view('admin/stylings/form', [
            'title'   => 'Sửa Styling',
            'styling' => $styling,
            'images'  => (new StylingImage())->forStyling((int) $id),
        ], 'admin');
    }

    public function update(string $id): void
    {
        Auth::requireRole('admin');
        Csrf::verify();
        (new Styling())->modify((int) $id, [
            'title'      => $this->input('title'),
            'model_info' => $this->input('model_info'),
            'sort_order' => $this->input('sort_order') ?: 0,
        ]);
        $this->saveImages((int) $id);
        Session::flash('success', 'Đã cập nhật styling.');
        $this->redirect('/admin/stylings/edit/' . (int) $id);
    }

    public function destroy(string $id): void
    {
        Auth::requireRole('admin');
        Csrf::verify();
        $model = new Styling();
        $imageModel = new StylingImage();
        foreach ($imageModel->forStyling((int) $id) as $img) {
            $file = dirname(__DIR__, 3) . '/public' . $img['image_url'];
            if (str_starts_with($img['image_url'], '/uploads/') && is_file($file)) {
                @unlink($file);
            }
        }
        $model->delete((int) $id);
        Session::flash('success', 'Đã xoá styling.');
        $this->redirect('/admin/stylings');
    }

    public function deleteImage(string $id): void
    {
        Auth::requireRole('admin');
        Csrf::verify();
        $imageModel = new StylingImage();
        $img = $imageModel->find((int) $id);
        if ($img) {
            $imageModel->deleteOne((int) $id);
            $file = dirname(__DIR__, 3) . '/public' . $img['image_url'];
            if (str_starts_with($img['image_url'], '/uploads/') && is_file($file)) {
                @unlink($file);
            }
        }
        $this->redirect('/admin/stylings/edit/' . ($img['styling_id'] ?? 0));
    }

    public function setCoverImage(string $id): void
    {
        Auth::requireRole('admin');
        Csrf::verify();
        $imageModel = new StylingImage();
        $img = $imageModel->find((int) $id);
        if ($img) {
            $imageModel->setCover((int) $id, (int) $img['styling_id']);
        }
        $this->redirect('/admin/stylings/edit/' . ($img['styling_id'] ?? 0));
    }

    public function reorderImages(): void
    {
        Auth::requireRole('admin');
        header('Content-Type: application/json');
        $body = json_decode(file_get_contents('php://input'), true);
        $ids  = array_filter((array) ($body['ids'] ?? []), 'is_numeric');
        if ($ids) {
            (new StylingImage())->reorder(array_values($ids));
        }
        echo json_encode(['ok' => true]);
        exit;
    }

    private function saveImages(int $stylingId): void
    {
        if (empty($_FILES['images']['name'][0])) {
            return;
        }
        $uploadDir = dirname(__DIR__, 3) . '/public/uploads/';
        if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }

        $imageModel = new StylingImage();
        $existing = $imageModel->forStyling($stylingId);
        $hasCover = !empty($existing);
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) { continue; }
            if ($_FILES['images']['size'][$i] > 5 * 1024 * 1024) { continue; } // 5MB
            $mime = mime_content_type($tmp);
            if (!in_array($mime, $allowed, true)) { continue; }
            $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
            $fname = bin2hex(random_bytes(8)) . '.' . preg_replace('/[^a-z0-9]/i', '', $ext);
            if (move_uploaded_file($tmp, $uploadDir . $fname)) {
                $imageModel->add($stylingId, '/uploads/' . $fname, !$hasCover);
                $hasCover = true;
            }
        }
    }
}
