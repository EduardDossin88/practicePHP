<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Lightweight HTTP router.
 */
final class Router
{
    /**
     * @var array<string, array<string, callable>>
     */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    /**
     * @param callable $handler
     */
    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    /**
     * @param callable $handler
     */
    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Not found'], JSON_THROW_ON_ERROR);
            return;
        }

        $handler();
    }
}
