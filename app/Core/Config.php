<?php

declare(strict_types=1);

namespace App\Core;

final class Config
{
    private static array $values = [];

    public static function load(string $envPath, string $fallbackPath): void
    {
        $values = [];
        if (is_file($fallbackPath)) {
            $values = array_merge($values, self::parseEnvFile($fallbackPath));
        }
        if (is_file($envPath)) {
            $values = array_merge($values, self::parseEnvFile($envPath));
        }
        if (!empty($_SERVER['HTTP_HOST']) || !empty($_SERVER['SERVER_NAME'])) {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $rawHost = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
            $host = preg_replace('/[^a-zA-Z0-9.:-]/', '', (string) $rawHost);
            $values['APP_URL'] = $scheme . '://' . $host;
        }
        self::$values = array_merge($values, self::$values);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$values[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::$values[$key] = $value;
    }

    public static function all(): array
    {
        return self::$values;
    }

    private static function parseEnvFile(string $path): array
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        $data = [];
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }
            if (!str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
                $value = trim($value, "\"'");
            }
            if ($value === 'true') {
                $value = true;
            } elseif ($value === 'false') {
                $value = false;
            }
            $data[$key] = $value;
        }
        return $data;
    }
}
