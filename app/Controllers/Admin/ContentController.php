<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Security;
use App\Core\AiService;
use App\Core\RateLimiter;
use App\Core\Config;
use PDO;

final class ContentController
{
    private function requireAuth(): void
    {
        Security::startSession();
        if (empty($_SESSION['admin_id'])) {
            redirect('/admin/login');
        }
    }

    public function articles(): void
    {
        $this->requireAuth();
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, title, status, created_at FROM articles ORDER BY created_at DESC');
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        view('admin/articles', ['articles' => $articles]);
    }

    public function editArticle(): void
    {
        $this->requireAuth();
        $pdo = Database::connection();
        $id = (int) ($_GET['id'] ?? 0);
        $stmt = $pdo->prepare('SELECT * FROM articles WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $pdo->prepare('SELECT id, name FROM categories ORDER BY name ASC');
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = $pdo->prepare('SELECT id, name FROM tags ORDER BY name ASC');
        $stmt->execute();
        $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
        view('admin/article-edit', ['article' => $article, 'categories' => $categories, 'tags' => $tags, 'csrf' => Security::csrfToken()]);
    }

    public function updateArticle(): void
    {
        $this->requireAuth();
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE articles SET title = :title, slug = :slug, category_id = :category_id, content_html = :content_html, featured_image = :featured_image, status = :status, seo_title = :seo_title, seo_description = :seo_description, updated_at = :updated_at, published_at = :published_at WHERE id = :id');
        $status = $_POST['status'] ?? 'draft';
        $stmt->execute([
            ':title' => $_POST['title'],
            ':slug' => $_POST['slug'],
            ':category_id' => $_POST['category_id'] ?: null,
            ':content_html' => Security::sanitizeHtml($_POST['content_html'] ?? ''),
            ':featured_image' => $_POST['featured_image'] ?? null,
            ':status' => $status,
            ':seo_title' => $_POST['seo_title'] ?? null,
            ':seo_description' => $_POST['seo_description'] ?? null,
            ':updated_at' => time(),
            ':published_at' => $status === 'published' ? time() : null,
            ':id' => (int) $_POST['id'],
        ]);
        redirect('/admin/articles');
    }

    public function createArticle(): void
    {
        $this->requireAuth();
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, name FROM categories ORDER BY name ASC');
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = $pdo->prepare('SELECT id, name FROM tags ORDER BY name ASC');
        $stmt->execute();
        $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
        view('admin/article-form', ['categories' => $categories, 'tags' => $tags, 'csrf' => Security::csrfToken()]);
    }

    public function storeArticle(): void
    {
        $this->requireAuth();
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $pdo = Database::connection();
        $title = trim((string) ($_POST['title'] ?? ''));
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $content = Security::sanitizeHtml($_POST['content_html'] ?: ($_POST['rewrite_choice'] ?? ''));
        $status = $_POST['status'] ?? 'draft';
        $stmt = $pdo->prepare('INSERT INTO articles (title, slug, category_id, content_html, featured_image, status, seo_title, seo_description, created_at, updated_at, published_at) VALUES (:title, :slug, :category_id, :content_html, :featured_image, :status, :seo_title, :seo_description, :created_at, :updated_at, :published_at)');
        $publishedAt = $status === 'published' ? time() : null;
        $stmt->execute([
            ':title' => $title,
            ':slug' => $slug,
            ':category_id' => $_POST['category_id'] ?: null,
            ':content_html' => $content,
            ':featured_image' => $_POST['featured_image'] ?? null,
            ':status' => $status,
            ':seo_title' => $_POST['seo_title'] ?? null,
            ':seo_description' => $_POST['seo_description'] ?? null,
            ':created_at' => time(),
            ':updated_at' => time(),
            ':published_at' => $publishedAt,
        ]);
        $articleId = (int) $pdo->lastInsertId();
        if (!empty($_POST['tags']) && is_array($_POST['tags'])) {
            $tagStmt = $pdo->prepare('INSERT INTO article_tags (article_id, tag_id) VALUES (:article_id, :tag_id)');
            foreach ($_POST['tags'] as $tagId) {
                $tagStmt->execute([':article_id' => $articleId, ':tag_id' => (int) $tagId]);
            }
        }
        redirect('/admin/articles');
    }

    public function aiArticle(): void
    {
        $this->requireAuth();
        view('admin/ai-article', ['csrf' => Security::csrfToken()]);
    }

    public function generateAi(): void
    {
        $this->requireAuth();
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $keyword = trim((string) ($_POST['keyword'] ?? ''));
        $ai = new AiService();
        try {
            $result = $ai->generateArticle($keyword);
            view('admin/ai-result', ['article' => $result, 'csrf' => Security::csrfToken()]);
        } catch (\RuntimeException $e) {
            view('admin/ai-article', ['csrf' => Security::csrfToken(), 'error' => $e->getMessage()]);
        }
    }

    public function rewriteAi(): void
    {
        $this->requireAuth();
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $content = (string) ($_POST['content'] ?? '');
        $ai = new AiService();
        try {
            $rewrites = $ai->rewriteArticle($content);
            view('admin/ai-rewrite', ['rewrites' => $rewrites, 'csrf' => Security::csrfToken(), 'original' => $content, 'featured_image' => $_POST['featured_image'] ?? '']);
        } catch (\RuntimeException $e) {
            view('admin/ai-article', ['csrf' => Security::csrfToken(), 'error' => $e->getMessage()]);
        }
    }

    public function categories(): void
    {
        $this->requireAuth();
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, name, slug, parent_id, featured_image FROM categories ORDER BY name ASC');
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        view('admin/categories', ['categories' => $categories, 'csrf' => Security::csrfToken()]);
    }

    public function editCategory(): void
    {
        $this->requireAuth();
        $pdo = Database::connection();
        $id = (int) ($_GET['id'] ?? 0);
        $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        view('admin/category-edit', ['category' => $category, 'csrf' => Security::csrfToken()]);
    }

    public function updateCategory(): void
    {
        $this->requireAuth();
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE categories SET name = :name, slug = :slug, parent_id = :parent_id, featured_image = :featured_image, seo_title = :seo_title, seo_description = :seo_description WHERE id = :id');
        $stmt->execute([
            ':name' => $_POST['name'],
            ':slug' => $_POST['slug'],
            ':parent_id' => $_POST['parent_id'] ?: null,
            ':featured_image' => $_POST['featured_image'] ?? null,
            ':seo_title' => $_POST['seo_title'] ?? null,
            ':seo_description' => $_POST['seo_description'] ?? null,
            ':id' => (int) $_POST['id'],
        ]);
        redirect('/admin/categories');
    }

    public function storeCategory(): void
    {
        $this->requireAuth();
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO categories (name, slug, parent_id, featured_image, seo_title, seo_description, created_at) VALUES (:name, :slug, :parent_id, :featured_image, :seo_title, :seo_description, :created_at)');
        $stmt->execute([
            ':name' => $_POST['name'],
            ':slug' => $_POST['slug'],
            ':parent_id' => $_POST['parent_id'] ?: null,
            ':featured_image' => $_POST['featured_image'] ?? null,
            ':seo_title' => $_POST['seo_title'] ?? null,
            ':seo_description' => $_POST['seo_description'] ?? null,
            ':created_at' => time(),
        ]);
        redirect('/admin/categories');
    }

    public function tags(): void
    {
        $this->requireAuth();
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, name, slug FROM tags ORDER BY name ASC');
        $stmt->execute();
        $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
        view('admin/tags', ['tags' => $tags, 'csrf' => Security::csrfToken()]);
    }

    public function editTag(): void
    {
        $this->requireAuth();
        $pdo = Database::connection();
        $id = (int) ($_GET['id'] ?? 0);
        $stmt = $pdo->prepare('SELECT * FROM tags WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $tag = $stmt->fetch(PDO::FETCH_ASSOC);
        view('admin/tag-edit', ['tag' => $tag, 'csrf' => Security::csrfToken()]);
    }

    public function updateTag(): void
    {
        $this->requireAuth();
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE tags SET name = :name, slug = :slug, seo_title = :seo_title, seo_description = :seo_description WHERE id = :id');
        $stmt->execute([
            ':name' => $_POST['name'],
            ':slug' => $_POST['slug'],
            ':seo_title' => $_POST['seo_title'] ?? null,
            ':seo_description' => $_POST['seo_description'] ?? null,
            ':id' => (int) $_POST['id'],
        ]);
        redirect('/admin/tags');
    }

    public function storeTag(): void
    {
        $this->requireAuth();
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO tags (name, slug, seo_title, seo_description, created_at) VALUES (:name, :slug, :seo_title, :seo_description, :created_at)');
        $stmt->execute([
            ':name' => $_POST['name'],
            ':slug' => $_POST['slug'],
            ':seo_title' => $_POST['seo_title'] ?? null,
            ':seo_description' => $_POST['seo_description'] ?? null,
            ':created_at' => time(),
        ]);
        redirect('/admin/tags');
    }

    public function comments(): void
    {
        $this->requireAuth();
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT c.id, c.content, c.status, c.created_at, a.title as article_title FROM comments c JOIN articles a ON c.article_id = a.id ORDER BY c.created_at DESC');
        $stmt->execute();
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        view('admin/comments', ['comments' => $comments, 'csrf' => Security::csrfToken()]);
    }

    public function updateComment(): void
    {
        $this->requireAuth();
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE comments SET status = :status WHERE id = :id');
        $stmt->execute([
            ':status' => $_POST['status'],
            ':id' => (int) $_POST['id'],
        ]);
        redirect('/admin/comments');
    }
}
