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
            $pdo->beginTransaction();
            try {
                $pdo->exec($sql);
                $stmt = $pdo->prepare('INSERT INTO migrations (name, applied_at) VALUES (:name, :applied_at)');
                $stmt->execute([':name' => $name, ':applied_at' => time()]);
                $pdo->commit();
            } catch (RuntimeException $e) {
                $pdo->rollBack();
                throw $e;
            }
        }
    }

    private static function migrationFiles(): array
    {
        return [
            '001_core_tables' => file_get_contents(BASE_PATH . '/app/migrations/001_core_tables.sql'),
        ];
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
}
