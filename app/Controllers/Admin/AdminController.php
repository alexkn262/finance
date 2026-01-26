<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Analytics;
use App\Core\Database;
use App\Core\Security;
use PDO;

final class AdminController
{
    private function requireAuth(): void
    {
        Security::startSession();
        if (empty($_SESSION['admin_id'])) {
            redirect('/admin/login');
        }
    }

    public function dashboard(): void
    {
        $this->requireAuth();
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM articles');
        $stmt->execute();
        $articles = (int) $stmt->fetchColumn();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM categories');
        $stmt->execute();
        $categories = (int) $stmt->fetchColumn();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM comments');
        $stmt->execute();
        $comments = (int) $stmt->fetchColumn();
        $stats = [
            'articles' => $articles,
            'categories' => $categories,
            'comments' => $comments,
            'page_views' => Analytics::pageViews(),
            'unique_visitors' => Analytics::uniqueVisitors(),
            'trends' => Analytics::trends(14),
        ];
        view('admin/dashboard', $stats);
    }

    public function analytics(): void
    {
        $this->requireAuth();
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT page, COUNT(*) as views, COUNT(DISTINCT ip_hash) as unique_visitors FROM analytics GROUP BY page ORDER BY views DESC');
        $stmt->execute();
        $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        view('admin/analytics', [
            'pages' => $pages,
        ]);
    }

    public function pageAnalytics(): void
    {
        $this->requireAuth();
        $page = $_GET['page'] ?? '/';
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT date(datetime(viewed_at, "unixepoch")) as day, COUNT(*) as views FROM analytics WHERE page = :page GROUP BY day ORDER BY day ASC');
        $stmt->execute([':page' => $page]);
        $trends = $stmt->fetchAll(PDO::FETCH_ASSOC);
        view('admin/analytics-page', [
            'page' => $page,
            'trends' => $trends,
        ]);
    }

    public function settings(): void
    {
        $this->requireAuth();
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT key, value FROM settings');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }
        view('admin/settings', ['settings' => $settings, 'csrf' => Security::csrfToken()]);
    }

    public function updateSettings(): void
    {
        $this->requireAuth();
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT OR REPLACE INTO settings (key, value) VALUES (:key, :value)');
        foreach (['site_name', 'site_url', 'seo_title', 'seo_description'] as $key) {
            if (isset($_POST[$key])) {
                $stmt->execute([':key' => $key, ':value' => $_POST[$key]]);
            }
        }
        redirect('/admin/settings');
    }
}
