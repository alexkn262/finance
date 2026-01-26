<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final class RateLimiter
{
    public static function check(string $key, int $limit, int $window): bool
    {
        $pdo = Database::connection();
        $pdo->exec('CREATE TABLE IF NOT EXISTS rate_limits (key TEXT PRIMARY KEY, attempts INTEGER, reset_at INTEGER)');
        $stmt = $pdo->prepare('SELECT attempts, reset_at FROM rate_limits WHERE key = :key');
        $stmt->execute([':key' => $key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $now = time();
        if (!$row) {
            $stmt = $pdo->prepare('INSERT INTO rate_limits (key, attempts, reset_at) VALUES (:key, 1, :reset_at)');
            $stmt->execute([':key' => $key, ':reset_at' => $now + $window]);
            return true;
        }
        if ($row['reset_at'] < $now) {
            $stmt = $pdo->prepare('UPDATE rate_limits SET attempts = 1, reset_at = :reset_at WHERE key = :key');
            $stmt->execute([':key' => $key, ':reset_at' => $now + $window]);
            return true;
        }
        if ($row['attempts'] >= $limit) {
            return false;
        }
        $stmt = $pdo->prepare('UPDATE rate_limits SET attempts = attempts + 1 WHERE key = :key');
        $stmt->execute([':key' => $key]);
        return true;
    }
}
