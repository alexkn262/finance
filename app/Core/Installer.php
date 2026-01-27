<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use RuntimeException;

final class Installer
{
    public static function isInstalled(): bool
    {
        return is_file(self::lockPath());
    }

    public static function lock(): void
    {
        file_put_contents(self::lockPath(), 'installed');
    }

    public static function lockPath(): string
    {
        return BASE_PATH . '/storage/installed.lock';
    }

    public static function requirements(): array
    {
        return [
            'php_version' => PHP_VERSION_ID >= 80200,
            'pdo_sqlite' => extension_loaded('pdo_sqlite'),
            'sqlite_version' => class_exists(PDO::class),
            'storage_writable' => is_writable(BASE_PATH . '/storage'),
        ];
    }

    public static function runMigrations(): void
    {
        $pdo = Database::connection();
        $pdo->exec('CREATE TABLE IF NOT EXISTS migrations (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT UNIQUE, applied_at INTEGER)');
        $migrations = self::migrationFiles();
        foreach ($migrations as $name => $sql) {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM migrations WHERE name = :name');
            $stmt->execute([':name' => $name]);
            if ($stmt->fetchColumn() > 0) {
                continue;
            }
            if ($name === '004_category_featured_image' && self::columnExists($pdo, 'categories', 'featured_image')) {
                $stmt = $pdo->prepare('INSERT INTO migrations (name, applied_at) VALUES (:name, :applied_at)');
                $stmt->execute([':name' => $name, ':applied_at' => time()]);
                continue;
            }
            $pdo->beginTransaction();
            try {
                $pdo->exec($sql);
                $stmt = $pdo->prepare('INSERT INTO migrations (name, applied_at) VALUES (:name, :applied_at)');
                $stmt->execute([':name' => $name, ':applied_at' => time()]);
                $pdo->commit();
            } catch (\Throwable $e) {
                $pdo->rollBack();
                throw $e;
            }
        }
    }

    private static function migrationFiles(): array
    {
        $path = BASE_PATH . '/app/migrations/001_core_tables.sql';
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new RuntimeException('Migration file missing: ' . $path);
        }
        $path2 = BASE_PATH . '/app/migrations/002_newsletter_contact.sql';
        $contents2 = file_get_contents($path2);
        if ($contents2 === false) {
            throw new RuntimeException('Migration file missing: ' . $path2);
        }
        $path3 = BASE_PATH . '/app/migrations/003_article_likes.sql';
        $contents3 = file_get_contents($path3);
        if ($contents3 === false) {
            throw new RuntimeException('Migration file missing: ' . $path3);
        }
        $path4 = BASE_PATH . '/app/migrations/004_category_featured_image.sql';
        $contents4 = file_get_contents($path4);
        if ($contents4 === false) {
            throw new RuntimeException('Migration file missing: ' . $path4);
        }
        return [
            '001_core_tables' => $contents,
            '002_newsletter_contact' => $contents2,
            '003_article_likes' => $contents3,
            '004_category_featured_image' => $contents4,
        ];
    }

    private static function columnExists(PDO $pdo, string $table, string $column): bool
    {
        $stmt = $pdo->prepare("PRAGMA table_info({$table})");
        $stmt->execute();
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (($row['name'] ?? null) === $column) {
                return true;
            }
        }
        return false;
    }

    public static function setupAdmin(string $name, string $email, string $password): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO admins (name, email, password_hash, created_at) VALUES (:name, :email, :hash, :created_at)');
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':hash' => Security::hashPassword($password),
            ':created_at' => time(),
        ]);
    }

    public static function saveSettings(array $settings): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO settings (key, value) VALUES (:key, :value)');
        foreach ($settings as $key => $value) {
            $stmt->execute([':key' => $key, ':value' => $value]);
        }
    }

    public static function seedDemoContent(): void
    {
        $pdo = Database::connection();
        $now = time();
        $categoryStmt = $pdo->prepare('INSERT INTO categories (name, slug, parent_id, featured_image, seo_title, seo_description, created_at) VALUES (:name, :slug, :parent_id, :featured_image, :seo_title, :seo_description, :created_at)');
        $categoryStmt->execute([
            ':name' => 'Smart Investing',
            ':slug' => 'smart-investing',
            ':parent_id' => null,
            ':featured_image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1200&q=80',
            ':seo_title' => 'Smart Investing Guides',
            ':seo_description' => 'Evidence-based investment strategies, portfolio structure, and long-term planning.',
            ':created_at' => $now,
        ]);
        $investingId = (int) $pdo->lastInsertId();
        $categoryStmt->execute([
            ':name' => 'Money Fundamentals',
            ':slug' => 'money-fundamentals',
            ':parent_id' => null,
            ':featured_image' => 'https://images.unsplash.com/photo-1489515217757-5fd1be406fef?auto=format&fit=crop&w=1200&q=80',
            ':seo_title' => 'Money Fundamentals',
            ':seo_description' => 'Core money skills for budgeting, saving, and building a strong foundation.',
            ':created_at' => $now,
        ]);
        $fundamentalsId = (int) $pdo->lastInsertId();

        $articleStmt = $pdo->prepare('INSERT INTO articles (title, slug, category_id, content_html, featured_image, status, seo_title, seo_description, ai_generated, created_at, updated_at, published_at) VALUES (:title, :slug, :category_id, :content_html, :featured_image, :status, :seo_title, :seo_description, :ai_generated, :created_at, :updated_at, :published_at)');
        $articleStmt->execute([
            ':title' => 'The Smart Investor Blueprint',
            ':slug' => 'smart-investor-blueprint',
            ':category_id' => $investingId,
            ':content_html' => Security::sanitizeHtml('<h2>Build a resilient portfolio</h2><p>Learn how to structure a portfolio using diversified funds, rebalancing rhythms, and risk-aware allocation that fits your timeline.</p><h3>Key takeaways</h3><ul><li>Define your risk band and time horizon.</li><li>Use low-cost index funds as a core.</li><li>Rebalance quarterly to stay aligned.</li></ul>'),
            ':featured_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1400&q=80',
            ':status' => 'published',
            ':seo_title' => 'The Smart Investor Blueprint',
            ':seo_description' => 'A step-by-step guide to building a resilient portfolio with diversified index funds and clear allocation targets.',
            ':ai_generated' => 0,
            ':created_at' => $now,
            ':updated_at' => $now,
            ':published_at' => $now,
        ]);
        $articleStmt->execute([
            ':title' => 'Money Basics: Your First 90 Days',
            ':slug' => 'money-basics-first-90-days',
            ':category_id' => $fundamentalsId,
            ':content_html' => Security::sanitizeHtml('<h2>Stabilize your cash flow</h2><p>Map your income, build a lean budget, and automate savings to create momentum in the first three months.</p><h3>Quick wins</h3><ul><li>Track every expense weekly.</li><li>Automate a 10% savings rule.</li><li>Reduce fixed costs in week two.</li></ul>'),
            ':featured_image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=1400&q=80',
            ':status' => 'published',
            ':seo_title' => 'Money Basics: Your First 90 Days',
            ':seo_description' => 'A 90-day roadmap to stabilize spending, automate savings, and build sustainable money habits.',
            ':ai_generated' => 0,
            ':created_at' => $now,
            ':updated_at' => $now,
            ':published_at' => $now,
        ]);
    }
}
