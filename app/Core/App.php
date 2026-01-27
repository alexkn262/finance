<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\Admin\AdminController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\ContentController;
use App\Controllers\Admin\InstallerController;
use App\Controllers\PublicController;
use PDO;

final class App
{
    private Router $router;

    public static function boot(): void
    {
        Security::initHeaders();
        Security::startSession();
        $pdo = Database::connection();
        if (Installer::isInstalled()) {
            Installer::runMigrations();
            $stmt = $pdo->prepare('SELECT key, value FROM settings');
            $stmt->execute();
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                Config::set($row['key'], $row['value']);
            }
        }
    }

    public function __construct()
    {
        $this->router = new Router();
        $this->registerRoutes();
    }

    public function run(): void
    {
        $this->router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
    }

    private function registerRoutes(): void
    {
        $this->router->get('/', [new PublicController(), 'home']);
        $this->router->get('/start-here', [new PublicController(), 'startHere']);
        $this->router->get('/blog', [new PublicController(), 'blog']);
        $this->router->getPattern('#^/blog/([^/]+)$#', [new PublicController(), 'article']);
        $this->router->getPattern('#^/blog/([^/]+)/amp$#', [new PublicController(), 'articleAmp']);
        $this->router->get('/tools', [new PublicController(), 'tools']);
        $this->router->get('/tools/compound-interest', [new PublicController(), 'compoundInterest']);
        $this->router->get('/tools/loan-calculator', [new PublicController(), 'loanCalculator']);
        $this->router->get('/tools/fire-calculator', [new PublicController(), 'fireCalculator']);
        $this->router->get('/tools/inflation-calculator', [new PublicController(), 'inflationCalculator']);
        $this->router->get('/search', [new PublicController(), 'search']);
        $this->router->post('/comment', [new PublicController(), 'submitComment']);
        $this->router->post('/newsletter', [new PublicController(), 'subscribeNewsletter']);
        $this->router->get('/sitemap.xml', [new PublicController(), 'sitemap']);
        $this->router->get('/robots.txt', [new PublicController(), 'robots']);
        $this->router->get('/contact', [new PublicController(), 'contact']);
        $this->router->post('/contact', [new PublicController(), 'submitContact']);
        $this->router->get('/privacy', [new PublicController(), 'privacy']);
        $this->router->get('/terms', [new PublicController(), 'terms']);
        $this->router->get('/unsubscribe', [new PublicController(), 'unsubscribe']);
        $this->router->get('/admin/analytics', [new AdminController(), 'analytics']);
        $this->router->get('/admin/analytics/page', [new AdminController(), 'pageAnalytics']);
        $this->router->get('/admin/analytics/data', [new AdminController(), 'analyticsData']);
        $this->router->get('/admin/newsletter', [new AdminController(), 'newsletter']);
        $this->router->post('/admin/newsletter/send', [new AdminController(), 'sendNewsletter']);
        $this->router->post('/admin/newsletter/delete', [new AdminController(), 'deleteSubscriber']);
        $this->router->get('/admin/ads', [new AdminController(), 'ads']);
        $this->router->post('/admin/ads', [new AdminController(), 'saveAds']);

        $this->router->get('/admin', [new AdminController(), 'dashboard']);
        $this->router->get('/admin/login', [new AuthController(), 'showLogin']);
        $this->router->post('/admin/login', [new AuthController(), 'login']);
        $this->router->post('/admin/logout', [new AuthController(), 'logout']);
        $this->router->get('/admin/articles', [new ContentController(), 'articles']);
        $this->router->get('/admin/articles/create', [new ContentController(), 'createArticle']);
        $this->router->post('/admin/articles/create', [new ContentController(), 'storeArticle']);
        $this->router->get('/admin/articles/edit', [new ContentController(), 'editArticle']);
        $this->router->post('/admin/articles/edit', [new ContentController(), 'updateArticle']);
        $this->router->get('/admin/articles/ai', [new ContentController(), 'aiArticle']);
        $this->router->post('/admin/articles/ai', [new ContentController(), 'generateAi']);
        $this->router->post('/admin/articles/rewrite', [new ContentController(), 'rewriteAi']);
        $this->router->get('/admin/categories', [new ContentController(), 'categories']);
        $this->router->post('/admin/categories', [new ContentController(), 'storeCategory']);
        $this->router->get('/admin/categories/edit', [new ContentController(), 'editCategory']);
        $this->router->post('/admin/categories/edit', [new ContentController(), 'updateCategory']);
        $this->router->get('/admin/tags', [new ContentController(), 'tags']);
        $this->router->post('/admin/tags', [new ContentController(), 'storeTag']);
        $this->router->get('/admin/tags/edit', [new ContentController(), 'editTag']);
        $this->router->post('/admin/tags/edit', [new ContentController(), 'updateTag']);
        $this->router->get('/admin/comments', [new ContentController(), 'comments']);
        $this->router->post('/admin/comments/update', [new ContentController(), 'updateComment']);
        $this->router->get('/admin/settings', [new AdminController(), 'settings']);
        $this->router->post('/admin/settings', [new AdminController(), 'updateSettings']);

        $this->router->get('/install', [new InstallerController(), 'wizard']);
        $this->router->post('/install', [new InstallerController(), 'complete']);
    }
}
