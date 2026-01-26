<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];
    private array $patterns = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function getPattern(string $pattern, callable $handler): void
    {
        $this->patterns['GET'][$pattern] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if ($extension !== '') {
            http_response_code(404);
            return;
        }
        $handler = $this->routes[$method][$path] ?? null;
        if ($handler) {
            call_user_func($handler);
            return;
        }
        foreach ($this->patterns[$method] ?? [] as $pattern => $patternHandler) {
            if (preg_match($pattern, $path, $matches)) {
                call_user_func($patternHandler, $matches);
                return;
            }
        }
        http_response_code(404);
        view('errors/404');
    }
}
