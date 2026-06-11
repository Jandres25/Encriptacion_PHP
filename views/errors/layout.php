<?php
/**
 * @param int    $code    HTTP status code
 * @param string $title   Short title (e.g. "Not Found")
 * @param string $message User-facing description
 * @param string $icon    FontAwesome icon class
 */
defined('APP_URL') || define('APP_URL', '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $code ?> — <?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/css/bootstrap.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/estilo.css">
    <link href="<?= APP_URL ?>/img/candado.png" rel="shortcut icon">
    <style>
        html, body { height: 100%; }
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--color-dark) 0%, var(--color-accent) 100%);
        }
        .error-card {
            background: rgba(255,255,255,.97);
            border-radius: 1rem;
            padding: 3rem 2.5rem;
            text-align: center;
            max-width: 460px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
        }
        .error-code {
            font-size: 6rem;
            font-weight: 800;
            line-height: 1;
            color: var(--color-dark);
            letter-spacing: -4px;
        }
        .error-icon {
            font-size: 2.5rem;
            color: var(--color-accent);
            margin-bottom: 1rem;
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-dark);
            margin-bottom: .5rem;
        }
        .error-message {
            color: #6c757d;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
<div class="error-page">
    <div class="error-card">
        <div class="error-code"><?= $code ?></div>
        <div class="error-icon mt-3"><i class="fas <?= htmlspecialchars($icon) ?>"></i></div>
        <div class="error-title"><?= htmlspecialchars($title) ?></div>
        <p class="error-message"><?= htmlspecialchars($message) ?></p>
        <a href="<?= APP_URL ?>/" class="btn btn-app-primary px-4 py-2">
            <i class="fas fa-home mr-1"></i> Back to Home
        </a>
    </div>
</div>
</body>
</html>
