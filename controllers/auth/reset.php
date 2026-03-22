<?php

require_once __DIR__ . '/AuthController.php';

(new App\Controller\Auth\AuthController($connection))->forgotPassword();
