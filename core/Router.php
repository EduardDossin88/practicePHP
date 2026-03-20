<?php

namespace Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $callback): void
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post(string $path, callable|array $callback): void
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function resolve(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);

        $callback = $this->routes[$method][$path] ?? null;

        if ($callback === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Маршрут не найден (404)']);
            return;
        }

        call_user_func($callback);
    }
}