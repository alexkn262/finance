<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Analytics;
use App\Core\Cache;
use App\Core\Config;
use App\Core\Database;
use App\Core\Installer;
use App\Core\RateLimiter;
use App\Core\Security;
use PDO;

final class PublicController
{
    private function ensureInstalled(): void
    {
        if (!Installer::isInstalled()) {
            redirect('/install');
        }
    }

    public function home(): void
    {
        $this->ensureInstalled();
        Analytics::track('/');
        $pdo = Database::connection();
        $ttl = (int) Config::get('CACHE_TTL', 300);
        $articles = Cache::remember('home_articles', $ttl, function () use ($pdo) {
            $stmt = $pdo->prepare("SELECT a.id, a.title, a.slug, a.seo_description, a.featured_image, a.content_html, a.created_at, c.name as category_name, c.slug as category_slug, (SELECT COUNT(*) FROM analytics WHERE page = '/blog/' || a.slug) as view_count FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.status = 'published' ORDER BY a.published_at DESC LIMIT :limit");
            $stmt->bindValue(':limit', 6, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        });
        $categories = Cache::remember('home_categories', $ttl, function () use ($pdo) {
            $categoryStmt = $pdo->prepare('SELECT id, name, slug FROM categories ORDER BY name ASC');
            $categoryStmt->execute();
            return $categoryStmt->fetchAll();
        });
        $tools = [
            ['title' => 'Compound Interest Calculator', 'url' => '/tools/compound-interest'],
            ['title' => 'Loan Payment Calculator', 'url' => '/tools/loan-calculator'],
            ['title' => 'FIRE Calculator', 'url' => '/tools/fire-calculator'],
            ['title' => 'Real Inflation Calculator', 'url' => '/tools/inflation-calculator'],
        ];
        view('home', [
            'articles' => $articles,
            'categories' => $categories,
            'tools' => $tools,
            'siteName' => Config::get('APP_NAME', 'Finance'),
        ]);
    }

    public function startHere(): void
    {
        $this->ensureInstalled();
        Analytics::track('/start-here');
        view('start-here');
    }

    public function blog(): void
    {
        $this->ensureInstalled();
        Analytics::track('/blog');
        $pdo = Database::connection();
        $category = $_GET['category'] ?? null;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 6;
        $offset = ($page - 1) * $limit;
        $categoryInfo = null;
        if ($category) {
            $stmt = $pdo->prepare("SELECT a.id, a.title, a.slug, a.seo_description, a.featured_image, a.content_html, a.created_at, c.name as category_name, c.slug as category_slug, (SELECT COUNT(*) FROM analytics WHERE page = '/blog/' || a.slug) as view_count FROM articles a JOIN categories c ON a.category_id = c.id WHERE a.status = 'published' AND c.slug = :slug ORDER BY a.published_at DESC LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':slug', $category, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $articles = $stmt->fetchAll();
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM articles a JOIN categories c ON a.category_id = c.id WHERE a.status = 'published' AND c.slug = :slug");
            $countStmt->execute([':slug' => $category]);
            $total = (int) $countStmt->fetchColumn();
            $infoStmt = $pdo->prepare('SELECT name, seo_description FROM categories WHERE slug = :slug');
            $infoStmt->execute([':slug' => $category]);
            $categoryInfo = $infoStmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } else {
            $stmt = $pdo->prepare("SELECT id, title, slug, seo_description, featured_image, content_html, created_at, (SELECT COUNT(*) FROM analytics WHERE page = '/blog/' || slug) as view_count FROM articles WHERE status = 'published' ORDER BY published_at DESC LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $articles = $stmt->fetchAll();
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE status = 'published'");
            $countStmt->execute();
            $total = (int) $countStmt->fetchColumn();
        }
        $totalPages = (int) ceil($total / $limit);
        $categoryStmt = $pdo->prepare('SELECT name, slug FROM categories ORDER BY name ASC');
        $categoryStmt->execute();
        $categories = $categoryStmt->fetchAll();
        view('blog', [
            'articles' => $articles,
            'categories' => $categories,
            'currentCategory' => $category,
            'page' => $page,
            'totalPages' => $totalPages,
            'categoryInfo' => $categoryInfo,
            'totalArticles' => $total,
        ]);
    }

    public function article(array $matches): void
    {
        $this->ensureInstalled();
        $slug = trim($matches[1] ?? '', '/');
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT a.id, a.title, a.content_html, a.seo_title, a.seo_description, a.featured_image, a.created_at, c.name as category_name, c.slug as category_slug, (SELECT COUNT(*) FROM analytics WHERE page = '/blog/' || a.slug) as view_count FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.slug = :slug AND a.status = 'published' LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$article) {
            http_response_code(404);
            view('errors/404');
            return;
        }
        Analytics::track('/blog/' . $slug);
        $commentStmt = $pdo->prepare("SELECT author_name, content, created_at FROM comments WHERE article_id = :id AND status = 'approved' ORDER BY created_at DESC");
        $commentStmt->execute([':id' => $article['id']]);
        $comments = $commentStmt->fetchAll();
        $stmt = $pdo->prepare('SELECT slug, title FROM articles WHERE status = "published" AND id < :id ORDER BY id DESC LIMIT 1');
        $stmt->execute([':id' => $article['id']]);
        $prev = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $pdo->prepare('SELECT slug, title FROM articles WHERE status = "published" AND id > :id ORDER BY id ASC LIMIT 1');
        $stmt->execute([':id' => $article['id']]);
        $next = $stmt->fetch(PDO::FETCH_ASSOC);
        view('article', [
            'article' => $article,
            'comments' => $comments,
            'csrf' => Security::csrfToken(),
            'seoTitle' => $article['seo_title'] ?: $article['title'],
            'seoDescription' => $article['seo_description'] ?? '',
            'prev' => $prev,
            'next' => $next,
            'ampUrl' => base_url('/blog/' . $slug . '/amp'),
        ]);
    }

    public function articleAmp(array $matches): void
    {
        $this->ensureInstalled();
        $slug = trim($matches[1] ?? '', '/');
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT a.id, a.title, a.content_html, a.featured_image, a.created_at, c.name as category_name, c.slug as category_slug FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.slug = :slug AND a.status = 'published' LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$article) {
            http_response_code(404);
            view('errors/404');
            return;
        }
        view('article-amp', ['article' => $article]);
    }

    public function tools(): void
    {
        $this->ensureInstalled();
        Analytics::track('/tools');
        view('tools/index');
    }

    public function compoundInterest(): void
    {
        $this->ensureInstalled();
        Analytics::track('/tools/compound-interest');
        view('tools/compound-interest');
    }

    public function loanCalculator(): void
    {
        $this->ensureInstalled();
        Analytics::track('/tools/loan-calculator');
        view('tools/loan-calculator');
    }

    public function fireCalculator(): void
    {
        $this->ensureInstalled();
        Analytics::track('/tools/fire-calculator');
        view('tools/fire-calculator');
    }

    public function inflationCalculator(): void
    {
        $this->ensureInstalled();
        Analytics::track('/tools/inflation-calculator');
        view('tools/inflation-calculator');
    }

    public function search(): void
    {
        $this->ensureInstalled();
        $term = trim((string) ($_GET['q'] ?? ''));
        $term = addcslashes($term, "%_");
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT title, slug FROM articles WHERE status = 'published' AND title LIKE :term LIMIT 10");
        $stmt->execute([':term' => '%' . $term . '%']);
        $results = $stmt->fetchAll();
        header('Content-Type: application/json');
        echo json_encode(['results' => $results]);
    }

    public function sitemap(): void
    {
        $this->ensureInstalled();
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT slug FROM articles WHERE status = 'published' ORDER BY published_at DESC");
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        view('sitemap', ['articles' => $articles]);
    }

    public function robots(): void
    {
        $this->ensureInstalled();
        header('Content-Type: text/plain');
        view('robots');
    }

    public function contact(): void
    {
        $this->ensureInstalled();
        Analytics::track('/contact');
        view('contact', ['csrf' => Security::csrfToken()]);
    }

    public function submitContact(): void
    {
        $this->ensureInstalled();
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (:name, :email, :subject, :message, :created_at)');
        $stmt->execute([
            ':name' => $_POST['name'],
            ':email' => $_POST['email'],
            ':subject' => $_POST['subject'] ?? null,
            ':message' => $_POST['message'],
            ':created_at' => time(),
        ]);
        redirect('/contact');
    }

    public function privacy(): void
    {
        $this->ensureInstalled();
        Analytics::track('/privacy');
        view('privacy');
    }

    public function terms(): void
    {
        $this->ensureInstalled();
        Analytics::track('/terms');
        view('terms');
    }

    public function unsubscribe(): void
    {
        $this->ensureInstalled();
        $token = $_GET['token'] ?? '';
        if ($token === '') {
            redirect('/');
        }
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE newsletter_subscribers SET status = :status, updated_at = :updated_at WHERE unsubscribe_token = :token');
        $stmt->execute([
            ':status' => 'unsubscribed',
            ':updated_at' => time(),
            ':token' => $token,
        ]);
        redirect('/');
    }

    public function submitComment(): void
    {
        $this->ensureInstalled();
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $limit = (int) Config::get('RATE_LIMIT_MAX', 5);
        $window = (int) Config::get('RATE_LIMIT_WINDOW', 60);
        if (!RateLimiter::check('comment:' . $ip, $limit, $window)) {
            http_response_code(429);
            exit('Too many requests');
        }
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO comments (article_id, author_name, author_email, content, status, ip_hash, created_at) VALUES (:article_id, :author_name, :author_email, :content, :status, :ip_hash, :created_at)');
        $stmt->execute([
            ':article_id' => (int) $_POST['article_id'],
            ':author_name' => $_POST['author_name'],
            ':author_email' => $_POST['author_email'],
            ':content' => Security::sanitizeHtml($_POST['content']),
            ':status' => 'pending',
            ':ip_hash' => Analytics::hashIp($ip),
            ':created_at' => time(),
        ]);
        redirect('/blog');
    }

    public function subscribeNewsletter(): void
    {
        $this->ensureInstalled();
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $email = (string) ($_POST['email'] ?? '');
        if (!Security::isValidEmail($email)) {
            http_response_code(422);
            exit('Invalid email');
        }
        $pdo = Database::connection();
        $token = bin2hex(random_bytes(16));
        $stmt = $pdo->prepare('INSERT INTO newsletter_subscribers (email, status, unsubscribe_token, created_at, updated_at) VALUES (:email, :status, :token, :created_at, :updated_at) ON CONFLICT(email) DO UPDATE SET status = :status, unsubscribe_token = :token, updated_at = :updated_at');
        $stmt->execute([
            ':email' => $email,
            ':status' => 'subscribed',
            ':token' => $token,
            ':created_at' => time(),
            ':updated_at' => time(),
        ]);
        redirect('/');
    }
}
