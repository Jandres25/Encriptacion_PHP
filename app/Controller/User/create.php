<?php

require_once __DIR__ . '/UserController.php';

(new App\Controller\User\UserController($connection))->create();
