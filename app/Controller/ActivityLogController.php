<?php

namespace App\Controller;

use App\Core\Controller;
use App\Middleware\AuthMiddleware;
use App\Model\ActivityLog;

class ActivityLogController extends Controller
{
    private ActivityLog $model;

    public function __construct(\mysqli $connection)
    {
        parent::__construct($connection);
        $this->model = new ActivityLog($connection);
    }

    public function index(): void
    {
        AuthMiddleware::timeout($this->connection);
        AuthMiddleware::admin();

        $this->render('activity-log/index.php', [
            'pageTitle'     => 'Activity Log — SecureAuth',
            'useDataTables' => true,
            'pageScripts'   => ['js/activity-logs-table.js'],
            'logs'          => $this->model->getAll(),
        ], protected: true);
    }
}
