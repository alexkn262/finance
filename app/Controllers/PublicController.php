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
            $categoryStmt = $pdo->prepare('SELECT id, name, slug, featured_image FROM categories ORDER BY name ASC');
            $categoryStmt->execute();
            return $categoryStmt->fetchAll();
        });
        $tools = [
            ['title' => 'Compound Interest Calculator', 'url' => '/tools/compound-interest'],
            ['title' => 'Loan Payment Calculator', 'url' => '/tools/loan-calculator'],
            ['title' => 'FIRE Calculator', 'url' => '/tools/fire-calculator'],
            ['title' => 'Real Inflation Calculator', 'url' => '/tools/inflation-calculator'],
        ];
        $seoImage = $articles[0]['featured_image'] ?? Config::get('seo_image');
        view('home', [
            'articles' => $articles,
            'categories' => $categories,
            'tools' => $tools,
            'siteName' => Config::get('APP_NAME', 'Finance'),
            'seoTitle' => Config::get('seo_home', 'Finance Education Platform'),
            'seoDescription' => Config::get('seo_description', 'Modern finance education, tools, and guides.'),
            'seoImage' => $seoImage ?: null,
            'seoType' => 'website',
            'schemaType' => 'WebPage',
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => base_url('/')],
            ],
            'schemaData' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => Config::get('seo_home', 'Finance Education Platform'),
                'description' => Config::get('seo_description', 'Modern finance education, tools, and guides.'),
                'url' => base_url('/'),
                'primaryImageOfPage' => [
                    '@type' => 'ImageObject',
                    'url' => $seoImage ?: Config::get('seo_image', ''),
                ],
                'isPartOf' => [
                    '@type' => 'WebSite',
                    'name' => Config::get('APP_NAME', 'Finance'),
                    'url' => base_url('/'),
                ],
            ],
        ]);
    }

    public function startHere(): void
    {
        $this->ensureInstalled();
        Analytics::track('/start-here');
        $pdo = Database::connection();
        $ttl = (int) Config::get('CACHE_TTL', 300);
        $categories = Cache::remember('start_here_categories', $ttl, function () use ($pdo) {
            $stmt = $pdo->prepare('SELECT name, slug, seo_description, featured_image FROM categories ORDER BY name ASC');
            $stmt->execute();
            return $stmt->fetchAll();
        });
        $seoImage = $categories[0]['featured_image'] ?? Config::get('seo_image');
        view('start-here', [
            'categories' => $categories,
            'seoTitle' => 'Start Here: Your Finance Roadmap',
            'seoDescription' => 'Beginner to advanced finance paths with curated categories and step-by-step guides.',
            'seoImage' => $seoImage ?: null,
            'schemaType' => 'WebPage',
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => base_url('/')],
                ['name' => 'Start Here'],
            ],
            'schemaData' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Start Here: Your Finance Roadmap',
                'description' => 'Beginner to advanced finance paths with curated categories and step-by-step guides.',
                'url' => base_url('/start-here'),
                'primaryImageOfPage' => [
                    '@type' => 'ImageObject',
                    'url' => $seoImage ?: Config::get('seo_image', ''),
                ],
                'isPartOf' => [
                    '@type' => 'WebSite',
                    'name' => Config::get('APP_NAME', 'Finance'),
                    'url' => base_url('/'),
                ],
            ],
        ]);
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
            $infoStmt = $pdo->prepare('SELECT name, seo_title, seo_description, featured_image FROM categories WHERE slug = :slug');
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
            $categoryStmt = $pdo->prepare('SELECT name, slug, featured_image FROM categories ORDER BY name ASC');
            $categoryStmt->execute();
            $categories = $categoryStmt->fetchAll();
        $seoTitle = $categoryInfo['seo_title'] ?? (($categoryInfo['name'] ?? 'Finance Guides') . ' Guides');
        $seoDescription = $categoryInfo['seo_description'] ?? 'Explore finance guides built for long-term wealth.';
        $seoImage = $categoryInfo['featured_image'] ?? ($articles[0]['featured_image'] ?? Config::get('seo_image'));
        $breadcrumbs = [
            ['name' => 'Home', 'url' => base_url('/')],
            ['name' => 'Guides', 'url' => base_url('/blog')],
        ];
        if ($categoryInfo) {
            $breadcrumbs[] = ['name' => $categoryInfo['name']];
        }
        view('blog', [
            'articles' => $articles,
            'categories' => $categories,
            'currentCategory' => $category,
            'page' => $page,
            'totalPages' => $totalPages,
            'categoryInfo' => $categoryInfo,
            'totalArticles' => $total,
            'seoTitle' => $seoTitle,
            'seoDescription' => $seoDescription,
            'seoImage' => $seoImage ?: null,
            'schemaType' => 'CollectionPage',
            'breadcrumbs' => $breadcrumbs,
            'schemaData' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $seoTitle,
                'description' => $seoDescription,
                'url' => base_url($category ? '/blog?category=' . $category : '/blog'),
                'hasPart' => array_map(static function (array $article): array {
                    return [
                        '@type' => 'Article',
                        'headline' => $article['title'],
                        'url' => base_url('/blog/' . $article['slug']),
                        'image' => $article['featured_image'] ?? null,
                    ];
                }, $articles),
                'isPartOf' => [
                    '@type' => 'WebSite',
                    'name' => Config::get('APP_NAME', 'Finance'),
                    'url' => base_url('/'),
                ],
            ],
        ]);
    }

    public function categories(): void
    {
        $this->ensureInstalled();
        Analytics::track('/categories');
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT c.id, c.name, c.slug, c.seo_description, c.featured_image, c.created_at, (SELECT COUNT(*) FROM articles a WHERE a.category_id = c.id AND a.status = 'published') as article_count FROM categories c ORDER BY c.name ASC");
        $stmt->execute();
        $categories = $stmt->fetchAll();
        $seoImage = $categories[0]['featured_image'] ?? Config::get('seo_image');
        view('categories', [
            'categories' => $categories,
            'seoTitle' => 'Finance Categories',
            'seoDescription' => 'Browse every finance category with featured guides and deep-dive learning paths.',
            'seoImage' => $seoImage ?: null,
            'schemaType' => 'CollectionPage',
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => base_url('/')],
                ['name' => 'Categories'],
            ],
            'schemaData' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => 'Finance Categories',
                'description' => 'Browse every finance category with featured guides and deep-dive learning paths.',
                'url' => base_url('/categories'),
                'hasPart' => array_map(static function (array $category): array {
                    return [
                        '@type' => 'CollectionPage',
                        'name' => $category['name'],
                        'url' => base_url('/blog?category=' . $category['slug']),
                        'image' => $category['featured_image'] ?? null,
                    ];
                }, $categories),
                'isPartOf' => [
                    '@type' => 'WebSite',
                    'name' => Config::get('APP_NAME', 'Finance'),
                    'url' => base_url('/'),
                ],
            ],
        ]);
    }

    public function article(array $matches): void
    {
        $this->ensureInstalled();
        $slug = trim($matches[1] ?? '', '/');
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT a.id, a.title, a.slug, a.content_html, a.seo_title, a.seo_description, a.featured_image, a.created_at, c.name as category_name, c.slug as category_slug, (SELECT COUNT(*) FROM analytics WHERE page = '/blog/' || a.slug) as view_count, (SELECT COUNT(*) FROM article_likes WHERE article_id = a.id) as like_count FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.slug = :slug AND a.status = 'published' LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$article) {
            http_response_code(404);
            view('errors/404', [
                'seoTitle' => 'Article not found',
                'seoDescription' => 'This finance guide is unavailable. Explore other finance articles and tools.',
                'breadcrumbs' => [
                    ['name' => 'Home', 'url' => base_url('/')],
                    ['name' => 'Guides', 'url' => base_url('/blog')],
                    ['name' => 'Not Found'],
                ],
            ]);
            return;
        }
        $article['slug'] = $article['slug'] ?? $slug;
        $article['content_html'] = Security::sanitizeHtml($article['content_html'] ?? '');
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
        $seoDescription = $article['seo_description'] ?: excerpt($article['content_html'] ?? '');
        view('article', [
            'article' => $article,
            'comments' => $comments,
            'csrf' => Security::csrfToken(),
            'seoTitle' => $article['seo_title'] ?: $article['title'],
            'seoDescription' => $seoDescription,
            'prev' => $prev,
            'next' => $next,
            'ampUrl' => base_url('/blog/' . $slug . '/amp'),
            'seoImage' => !empty($article['featured_image']) ? $article['featured_image'] : null,
            'seoType' => 'article',
            'seoPublished' => date('c', (int) $article['created_at']),
            'seoModified' => date('c', (int) $article['created_at']),
            'seoSection' => $article['category_name'] ?? null,
            'schemaType' => 'Article',
            'schemaData' => [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $article['title'],
                'description' => $seoDescription,
                'image' => $article['featured_image'] ? [$article['featured_image']] : null,
                'datePublished' => date('c', (int) $article['created_at']),
                'dateModified' => date('c', (int) $article['created_at']),
                'author' => [
                    '@type' => 'Organization',
                    'name' => 'Finance Editorial Team',
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => Config::get('APP_NAME', 'Finance'),
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => $article['featured_image'] ?? Config::get('seo_image', ''),
                    ],
                ],
                'mainEntityOfPage' => base_url('/blog/' . $slug),
                'articleSection' => $article['category_name'] ?? null,
                'isPartOf' => [
                    '@type' => 'WebSite',
                    'name' => Config::get('APP_NAME', 'Finance'),
                    'url' => base_url('/'),
                ],
            ],
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => base_url('/')],
                ['name' => 'Guides', 'url' => base_url('/blog')],
                ['name' => $article['category_name'] ?? 'Category', 'url' => base_url('/blog?category=' . ($article['category_slug'] ?? ''))],
                ['name' => $article['title']],
            ],
        ]);
    }

    public function articleAmp(array $matches): void
    {
        $this->ensureInstalled();
        $slug = trim($matches[1] ?? '', '/');
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT a.id, a.title, a.content_html, a.seo_description, a.featured_image, a.created_at, c.name as category_name, c.slug as category_slug FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.slug = :slug AND a.status = 'published' LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$article) {
            http_response_code(404);
            view('errors/404', [
                'seoTitle' => 'Article not found',
                'seoDescription' => 'This finance guide is unavailable. Explore other finance articles and tools.',
                'breadcrumbs' => [
                    ['name' => 'Home', 'url' => base_url('/')],
                    ['name' => 'Guides', 'url' => base_url('/blog')],
                    ['name' => 'Not Found'],
                ],
            ]);
            return;
        }
        $article['content_html'] = Security::sanitizeHtml($article['content_html'] ?? '');
        $article['slug'] = $slug;
        $seoDescription = $article['seo_description'] ?: excerpt($article['content_html'] ?? '');
        view('article-amp', [
            'article' => $article,
            'seoTitle' => $article['title'],
            'seoDescription' => $seoDescription,
            'seoImage' => !empty($article['featured_image']) ? $article['featured_image'] : null,
            'seoType' => 'article',
            'seoPublished' => date('c', (int) $article['created_at']),
            'seoModified' => date('c', (int) $article['created_at']),
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => base_url('/')],
                ['name' => 'Guides', 'url' => base_url('/blog')],
                ['name' => $article['category_name'] ?? 'Category', 'url' => base_url('/blog?category=' . ($article['category_slug'] ?? ''))],
                ['name' => $article['title']],
            ],
        ]);
    }

    public function tools(): void
    {
        $this->ensureInstalled();
        Analytics::track('/tools');
        view('tools/index', [
            'seoTitle' => config('seo_tools', 'Finance Tools'),
            'seoDescription' => 'Interactive finance calculators with transparent formulas and clear explanations.',
            'seoImage' => config('seo_image') ?: null,
            'schemaType' => 'CollectionPage',
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => base_url('/')],
                ['name' => 'Tools'],
            ],
            'schemaData' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => config('seo_tools', 'Finance Tools'),
                'description' => 'Interactive finance calculators with transparent formulas and clear explanations.',
                'url' => base_url('/tools'),
                'hasPart' => [
                    [
                        '@type' => 'WebPage',
                        'name' => 'Compound Interest Calculator',
                        'url' => base_url('/tools/compound-interest'),
                    ],
                    [
                        '@type' => 'WebPage',
                        'name' => 'Loan Payment Calculator',
                        'url' => base_url('/tools/loan-calculator'),
                    ],
                    [
                        '@type' => 'WebPage',
                        'name' => 'FIRE Calculator',
                        'url' => base_url('/tools/fire-calculator'),
                    ],
                    [
                        '@type' => 'WebPage',
                        'name' => 'Real Inflation Calculator',
                        'url' => base_url('/tools/inflation-calculator'),
                    ],
                ],
                'isPartOf' => [
                    '@type' => 'WebSite',
                    'name' => Config::get('APP_NAME', 'Finance'),
                    'url' => base_url('/'),
                ],
            ],
        ]);
    }

    public function compoundInterest(): void
    {
        $this->ensureInstalled();
        Analytics::track('/tools/compound-interest');
        view('tools/compound-interest', [
            'seoTitle' => 'Compound Interest Calculator',
            'seoDescription' => 'Calculate compound growth with transparent assumptions and step-by-step inputs.',
            'seoImage' => config('seo_image') ?: null,
            'schemaType' => 'WebPage',
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => base_url('/')],
                ['name' => 'Tools', 'url' => base_url('/tools')],
                ['name' => 'Compound Interest'],
            ],
            'schemaData' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Compound Interest Calculator',
                'description' => 'Calculate compound growth with transparent assumptions and step-by-step inputs.',
                'url' => base_url('/tools/compound-interest'),
            ],
        ]);
    }

    public function loanCalculator(): void
    {
        $this->ensureInstalled();
        Analytics::track('/tools/loan-calculator');
        view('tools/loan-calculator', [
            'seoTitle' => 'Loan Payment Calculator',
            'seoDescription' => 'Estimate monthly loan payments with clear step-by-step inputs and explanations.',
            'seoImage' => config('seo_image') ?: null,
            'schemaType' => 'WebPage',
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => base_url('/')],
                ['name' => 'Tools', 'url' => base_url('/tools')],
                ['name' => 'Loan Calculator'],
            ],
            'schemaData' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Loan Payment Calculator',
                'description' => 'Estimate monthly loan payments with clear step-by-step inputs and explanations.',
                'url' => base_url('/tools/loan-calculator'),
            ],
        ]);
    }

    public function fireCalculator(): void
    {
        $this->ensureInstalled();
        Analytics::track('/tools/fire-calculator');
        view('tools/fire-calculator', [
            'seoTitle' => 'FIRE Number Calculator',
            'seoDescription' => 'Calculate your financial independence number with guided assumptions.',
            'seoImage' => config('seo_image') ?: null,
            'schemaType' => 'WebPage',
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => base_url('/')],
                ['name' => 'Tools', 'url' => base_url('/tools')],
                ['name' => 'FIRE Calculator'],
            ],
            'schemaData' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'FIRE Number Calculator',
                'description' => 'Calculate your financial independence number with guided assumptions.',
                'url' => base_url('/tools/fire-calculator'),
            ],
        ]);
    }

    public function inflationCalculator(): void
    {
        $this->ensureInstalled();
        Analytics::track('/tools/inflation-calculator');
        view('tools/inflation-calculator', [
            'seoTitle' => 'Real Inflation Calculator',
            'seoDescription' => 'Understand inflation-adjusted value changes with step-by-step inputs.',
            'seoImage' => config('seo_image') ?: null,
            'schemaType' => 'WebPage',
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => base_url('/')],
                ['name' => 'Tools', 'url' => base_url('/tools')],
                ['name' => 'Inflation Calculator'],
            ],
            'schemaData' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Real Inflation Calculator',
                'description' => 'Understand inflation-adjusted value changes with step-by-step inputs.',
                'url' => base_url('/tools/inflation-calculator'),
            ],
        ]);
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
        view('contact', [
            'csrf' => Security::csrfToken(),
            'seoTitle' => 'Contact Finance Editorial Team',
            'seoDescription' => 'Reach our finance editorial team for questions, partnerships, or support.',
            'schemaType' => 'WebPage',
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => base_url('/')],
                ['name' => 'Contact'],
            ],
            'schemaData' => [
                '@context' => 'https://schema.org',
                '@type' => 'ContactPage',
                'name' => 'Contact Finance Editorial Team',
                'description' => 'Reach our finance editorial team for questions, partnerships, or support.',
                'url' => base_url('/contact'),
            ],
        ]);
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
        view('privacy', [
            'seoTitle' => 'Privacy Policy',
            'seoDescription' => 'Read how we protect your data and keep analytics privacy-first.',
            'schemaType' => 'WebPage',
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => base_url('/')],
                ['name' => 'Privacy'],
            ],
            'schemaData' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Privacy Policy',
                'description' => 'Read how we protect your data and keep analytics privacy-first.',
                'url' => base_url('/privacy'),
            ],
        ]);
    }

    public function terms(): void
    {
        $this->ensureInstalled();
        Analytics::track('/terms');
        view('terms', [
            'seoTitle' => 'Terms of Service',
            'seoDescription' => 'Review terms, conditions, and usage guidelines for the finance platform.',
            'schemaType' => 'WebPage',
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => base_url('/')],
                ['name' => 'Terms'],
            ],
            'schemaData' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Terms of Service',
                'description' => 'Review terms, conditions, and usage guidelines for the finance platform.',
                'url' => base_url('/terms'),
            ],
        ]);
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

    public function likeArticle(): void
    {
        $this->ensureInstalled();
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $articleId = (int) ($_POST['article_id'] ?? 0);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT OR IGNORE INTO article_likes (article_id, ip_hash, created_at) VALUES (:article_id, :ip_hash, :created_at)');
        $stmt->execute([
            ':article_id' => $articleId,
            ':ip_hash' => Analytics::hashIp($ip),
            ':created_at' => time(),
        ]);
        redirect('/blog/' . ($_POST['slug'] ?? ''));
    }
}
