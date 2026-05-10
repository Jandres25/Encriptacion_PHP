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

    protected function render(string $view, array $data = [], bool $protected = false): void
    {
        $data['year'] ??= date('Y');
        extract($data, EXTR_SKIP);
        $viewsPath = __DIR__ . '/../../views/';

        if ($protected) {
            require $viewsPath . 'layouts/header.php';
            require $viewsPath . ltrim($view, '/');
            require $viewsPath . 'layouts/footer.php';
        } else {
            require $viewsPath . ltrim($view, '/');
        }
    }
}
