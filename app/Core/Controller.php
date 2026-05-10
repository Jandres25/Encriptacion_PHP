<?php

namespace App\Core;

abstract class Controller
{
    public function __construct(protected \mysqli $connection) {}

    protected function redirect(string $path): void
    {
        $url = str_starts_with($path, 'http') ? $path : APP_URL . '/' . ltrim($path, '/');
        header("Location: {$url}");
        exit;
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../../views/' . ltrim($view, '/');
    }
}
