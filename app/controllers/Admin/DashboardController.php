<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;

class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireRole('admin');
        $db = Database::conn();

        $stats = [
            'revenue'  => (float) $db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled'")->fetchColumn(),
            'orders'   => (int) $db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
            'products' => (int) $db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
            'users'    => (int) $db->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn(),
        ];

        $this->view('admin/dashboard', [
            'title' => 'Dashboard',
            'stats' => $stats,
        ], 'admin');
    }

    /** API JSON cho Chart.js */
    public function stats(): void
    {
        Auth::requireRole('admin');
        $db = Database::conn();

        // Doanh thu 14 ngày gần nhất
        $revenue = $db->query(
            "SELECT DATE_FORMAT(created_at, '%d/%m') AS day, SUM(total) AS amount
             FROM orders
             WHERE status != 'cancelled' AND created_at >= NOW() - INTERVAL 14 DAY
             GROUP BY DATE(created_at) ORDER BY DATE(created_at)"
        )->fetchAll();

        // Đơn theo trạng thái
        $byStatus = $db->query(
            "SELECT status, COUNT(*) AS cnt FROM orders GROUP BY status"
        )->fetchAll();

        // Top 5 sản phẩm bán chạy
        $topProducts = $db->query(
            "SELECT oi.product_name AS name, SUM(oi.quantity) AS sold
             FROM order_items oi
             GROUP BY oi.product_name
             ORDER BY sold DESC LIMIT 5"
        )->fetchAll();

        $this->json([
            'revenue'     => $revenue,
            'byStatus'    => $byStatus,
            'topProducts' => $topProducts,
        ]);
    }
}
