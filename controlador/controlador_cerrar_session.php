<?php
require_once __DIR__ . '/../config/config.php';
session_start();
session_destroy();
header("location:" . APP_URL . "/login.php");
