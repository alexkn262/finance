<?php

declare(strict_types=1);

namespace App\Core;

final class Cache
{
    public static function remember(string $key, int $ttl, callable $callback): mixed
    {
        $path = self::path($key);
        if (is_file($path)) {
            $payload = json_decode((string) file_get_contents($path), true);
            if (is_array($payload) && ($payload['expires'] ?? 0) >= time()) {
                return $payload['value'] ?? null;
            }
        }
        $value = $callback();
        $payload = [
            'expires' => time() + $ttl,
            'value' => $value,
        ];
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        file_put_contents($path, json_encode($payload));
        return $value;
    }

    public static function forget(string $key): void
    {
        $path = self::path($key);
        if (is_file($path)) {
            unlink($path);
        }
    }

    private static function path(string $key): string
    {
        return BASE_PATH . '/storage/cache/' . sha1($key) . '.json';
    }
}
