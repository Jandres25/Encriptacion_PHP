<?php

namespace App\Controller;

use App\Core\Controller;
use App\Middleware\AuthMiddleware;

class HomeController extends Controller
{
    public function index(): void
    {
        AuthMiddleware::timeout($this->connection);
        AuthMiddleware::auth();

        $this->render('home/index.php', [
            'pageTitle' => 'Dashboard — SecureAuth',
            'favicon'   => 'boton-de-inicio.png',
            'bodyClass' => 'dashboard',
            'name'      => $_SESSION['name'],
            'isAdmin'   => $_SESSION['is_admin'],
        ], protected: true);
    }
}
