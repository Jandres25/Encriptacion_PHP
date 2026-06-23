<?php

namespace App\Model;

use App\Config\Database;
use App\Core\Model;

class ActivityLog extends Model
{
    public const EVENT_LOGIN_SUCCESS    = 'login_success';
    public const EVENT_LOGIN_FAILED     = 'login_failed';
    public const EVENT_LOGOUT           = 'logout';
    public const EVENT_PASSWORD_CHANGED = 'password_changed';
    public const EVENT_PASSWORD_RESET   = 'password_reset';
    public const EVENT_USER_CREATED     = 'user_created';
    public const EVENT_USER_UPDATED     = 'user_updated';
    public const EVENT_USER_DELETED     = 'user_deleted';

    public function __construct(\mysqli $connection)
    {
        parent::__construct($connection);
    }

    public static function log(string $event, string $description, ?int $userId = null): void
    {
        static::logTo(Database::getConnection(), $event, $description, $userId);
    }

    // Separated for test injection without loading autoload.php or using the singleton
    public static function logTo(\mysqli $db, string $event, string $description, ?int $userId = null): void
    {
        try {
            $ip   = $_SERVER['REMOTE_ADDR'] ?? null;
            $stmt = $db->prepare(
                "INSERT INTO activity_logs (user_id, event, description, ip_address) VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("isss", $userId, $event, $description, $ip);
            $stmt->execute();
            $stmt->close();
        } catch (\Throwable $e) {
            error_log("ActivityLog::log failed: " . $e->getMessage());
        }
    }

    public function getCountTodayByEvent(string $event): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS total
             FROM activity_logs
             WHERE event = ? AND DATE(created_at) = CURDATE()"
        );
        $stmt->bind_param('s', $event);
        $stmt->execute();
        $total = (int) $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();
        return $total;
    }

    public function getRecentEvents(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT al.id, al.created_at, al.event, al.description, al.ip_address,
                    COALESCE(CONCAT(u.first_name, ' ', u.last_name), 'Anónimo') AS user_name
             FROM activity_logs al
             LEFT JOIN users u ON u.id = al.user_id
             ORDER BY al.created_at DESC
             LIMIT ?"
        );
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
        $stmt->close();
        return $events;
    }

    public function getAll(): array
    {
        $result = $this->db->query(
            "SELECT al.id, al.created_at, al.event, al.description, al.ip_address,
                    COALESCE(CONCAT(u.first_name, ' ', u.last_name), 'Anónimo') AS user_name
             FROM activity_logs al
             LEFT JOIN users u ON u.id = al.user_id
             ORDER BY al.created_at DESC"
        );

        $logs = [];
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }
        return $logs;
    }
}
