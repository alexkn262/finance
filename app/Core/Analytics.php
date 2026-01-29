<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final class Analytics
{
    public static function track(string $page): void
    {
        $pdo = Database::connection();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $hash = self::hashIp($ip);
        $stmt = $pdo->prepare('INSERT INTO analytics (page, ip_hash, viewed_at) VALUES (:page, :ip_hash, :viewed_at)');
        $stmt->execute([
            ':page' => $page,
            ':ip_hash' => $hash,
            ':viewed_at' => time(),
        ]);
    }

    public static function hashIp(string $ip): string
    {
        $salt = Config::get('ANALYTICS_SALT', 'finance');
        return hash('sha256', $salt . $ip);
    }

    public static function pageViews(): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM analytics');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public static function uniqueVisitors(): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT COUNT(DISTINCT ip_hash) FROM analytics');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public static function trends(int $days = 7, bool $unique = false): array
    {
        $pdo = Database::connection();
        $since = time() - ($days * 86400);
        $metric = $unique ? 'COUNT(DISTINCT ip_hash)' : 'COUNT(*)';
        $stmt = $pdo->prepare('SELECT date(datetime(viewed_at, "unixepoch")) as day, ' . $metric . ' as views FROM analytics WHERE viewed_at >= :since GROUP BY day ORDER BY day ASC');
        $stmt->execute([':since' => $since]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
