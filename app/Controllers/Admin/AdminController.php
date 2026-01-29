<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Analytics;
use App\Core\Cache;
use App\Core\Config;
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
        $stmt = $pdo->prepare("SELECT title, status FROM articles ORDER BY created_at DESC LIMIT 5");
        $stmt->execute();
        $recentArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stats = [
            'articles' => $articles,
            'categories' => $categories,
            'comments' => $comments,
            'page_views' => Analytics::pageViews(),
            'unique_visitors' => Analytics::uniqueVisitors(),
            'trends' => Analytics::trends(14),
            'recentArticles' => $recentArticles,
        ];
        view('admin/dashboard', $stats);
    }

    public function analytics(): void
    {
        $this->requireAuth();
        $pdo = Database::connection();
        $since = time() - 86400;
        $stmt = $pdo->prepare('SELECT page, COUNT(*) as views FROM analytics WHERE viewed_at >= :since GROUP BY page ORDER BY views DESC LIMIT 10');
        $stmt->execute([':since' => $since]);
        $top24h = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = $pdo->prepare('SELECT page, COUNT(*) as views FROM analytics GROUP BY page ORDER BY views DESC LIMIT 10');
        $stmt->execute();
        $topAll = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $trends = Analytics::trends(7, true);
        view('admin/analytics', [
            'pages' => $top24h,
            'topAll' => $topAll,
            'trends' => $trends,
        ]);
    }

    public function analyticsData(): void
    {
        $this->requireAuth();
        header('Content-Type: application/json');
        echo json_encode([
            'trends' => Analytics::trends(7, true),
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

    public function newsletter(): void
    {
        $this->requireAuth();
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, email, status, created_at FROM newsletter_subscribers ORDER BY created_at DESC');
        $stmt->execute();
        $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM newsletter_subscribers WHERE status != :status');
        $stmt->execute([':status' => 'unsubscribed']);
        $count = (int) $stmt->fetchColumn();
        view('admin/newsletter', ['subscribers' => $subscribers, 'count' => $count, 'csrf' => Security::csrfToken()]);
    }

    public function deleteSubscriber(): void
    {
        $this->requireAuth();
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $pdo = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM newsletter_subscribers WHERE id = :id');
        $stmt->execute([':id' => (int) $_POST['id']]);
        redirect('/admin/newsletter');
    }

    public function sendNewsletter(): void
    {
        $this->requireAuth();
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $subject = $_POST['subject'] ?? 'Finance Newsletter';
        $body = $_POST['body'] ?? '';
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT email, unsubscribe_token FROM newsletter_subscribers WHERE status != :status');
        $stmt->execute([':status' => 'unsubscribed']);
        $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($subscribers as $subscriber) {
            $unsubscribeUrl = base_url('/unsubscribe?token=' . $subscriber['unsubscribe_token']);
            $message = $body . "\n\nUnsubscribe: " . $unsubscribeUrl;
            @mail($subscriber['email'], $subject, $message, 'From: ' . Config::get('MAIL_FROM', 'no-reply@example.com'));
        }
        redirect('/admin/newsletter');
    }

    public function ads(): void
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
        view('admin/ads', ['settings' => $settings, 'csrf' => Security::csrfToken()]);
    }

    public function saveAds(): void
    {
        $this->requireAuth();
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT OR REPLACE INTO settings (key, value) VALUES (:key, :value)');
        foreach (['ads_index', 'ads_article', 'ads_tools', 'ads_category', 'ads_sidebar'] as $key) {
            $stmt->execute([':key' => $key, ':value' => $_POST[$key] ?? '']);
        }
        redirect('/admin/ads');
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
        foreach (['site_name', 'site_url', 'seo_title', 'seo_description', 'seo_home', 'seo_tools', 'seo_image', 'ga4_id', 'adsense_code', 'openai_api_key', 'paraphrase_api_1', 'paraphrase_api_2', 'paraphrase_api_3', 'paraphrase_api_4'] as $key) {
            if (array_key_exists($key, $_POST)) {
                $stmt->execute([':key' => $key, ':value' => $_POST[$key]]);
            }
        }
        if (!empty($_POST['clear_cache'])) {
            $this->clearCache();
        }
        redirect('/admin/settings');
    }

    private function clearCache(): void
    {
        foreach (glob(BASE_PATH . '/storage/cache/*.json') as $file) {
            @unlink($file);
        }
    }
}
