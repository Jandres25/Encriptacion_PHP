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
            'name'    => $_SESSION['name'],
            'isAdmin' => $_SESSION['is_admin'],
        ]);
    }
}
