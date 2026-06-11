<?php

namespace App\Core;

class Router
{
    private array $routes = ['GET' => [], 'POST' => []];

    public function __construct(private \mysqli $connection) {}

    public function get(string $uri, array $action): void
    {
        $this->routes['GET'][$this->normalize($uri)] = $action;
    }

    public function post(string $uri, array $action): void
    {
        $this->routes['POST'][$this->normalize($uri)] = $action;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = $this->normalize(parse_url($uri, PHP_URL_PATH) ?? '/');
        $base = $this->normalize(parse_url(APP_URL, PHP_URL_PATH) ?? '');

        if ($base !== '/' && str_starts_with($path, $base)) {
            $path = $this->normalize(substr($path, strlen($base)));
        }

        $action = $this->routes[$method][$path] ?? null;

        if ($action === null) {
            http_response_code(404);
            require __DIR__ . '/../../views/errors/404.php';
            return;
        }

        [$class, $methodName] = $action;
        (new $class($this->connection))->{$methodName}();
    }

    private function normalize(string $uri): string
    {
        $uri = '/' . trim($uri, '/');
        return $uri === '/' ? '/' : rtrim($uri, '/');
    }
}
