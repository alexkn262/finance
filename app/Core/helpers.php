<?php

declare(strict_types=1);

use App\Core\Config;

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function base_url(string $path = ''): string
{
    $base = rtrim(Config::get('APP_URL', ''), '/');
    if ($base === '') {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base = $scheme . '://' . $host;
    }
    $path = ltrim($path, '/');
    return $path ? $base . '/' . $path : $base;
}

function asset_url(string $path): string
{
    return base_url(ltrim($path, '/'));
}

function current_url(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    return base_url(trim($uri, '/'));
}

function view(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $path = BASE_PATH . '/app/Views/' . $template . '.php';
    if (!is_file($path)) {
        throw new RuntimeException('View not found: ' . $template);
    }
    require $path;
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function csrf_token(): string
{
    return App\Core\Security::csrfToken();
}

function config(string $key, mixed $default = null): mixed
{
    return Config::get($key, $default);
}

function excerpt(string $text, int $limit = 140): string
{
    $plain = trim(strip_tags($text));
    if (strlen($plain) <= $limit) {
        return $plain;
    }
    return rtrim(substr($plain, 0, $limit)) . '...';
}
