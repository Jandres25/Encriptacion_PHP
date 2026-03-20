<?php

session_destroy();

header('Location: ' . APP_URL . '/?page=login');
exit;
