<?php

require_once __DIR__ . '/view_helpers.php';
require_once __DIR__ . '/cache.php';
require_once __DIR__ . '/database.php';

require_once __DIR__ . '/../Controller/auth/AuthController.php';
session_start();
(new \App\Controller\Auth\AuthController($connection))->restoreFromCookie();
