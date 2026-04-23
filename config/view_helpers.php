<?php

function renderView(string $viewPath, array $data = []): void
{
    extract($data, EXTR_SKIP);
    include __DIR__ . '/../views/' . ltrim($viewPath, '/');
}

function renderProtectedView(string $viewPath, array $data = []): void
{
    $data['year'] ??= date('Y');
    extract($data, EXTR_SKIP);

    include __DIR__ . '/../views/layouts/header.php';
    include __DIR__ . '/../views/' . ltrim($viewPath, '/');
    include __DIR__ . '/../views/layouts/footer.php';
}
