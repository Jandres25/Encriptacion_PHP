<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Model\ActivityLog;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ActivityLogTest extends TestCase
{
    private ActivityLog $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new ActivityLog(self::$db);
    }

    // --- log() ---

    #[Test]
    public function it_inserts_a_row_with_user_id(): void
    {
        $user = $this->createUser();

        ActivityLog::logTo(self::$db, ActivityLog::EVENT_LOGIN_SUCCESS, 'Login: testuser', $user['id']);

        $row = self::$db->query("SELECT * FROM activity_logs LIMIT 1")->fetch_assoc();
        $this->assertNotNull($row);
        $this->assertSame((string) $user['id'], (string) $row['user_id']);
        $this->assertSame(ActivityLog::EVENT_LOGIN_SUCCESS, $row['event']);
        $this->assertSame('Login: testuser', $row['description']);
    }

    #[Test]
    public function it_inserts_a_row_with_null_user_id(): void
    {
        ActivityLog::logTo(self::$db, ActivityLog::EVENT_LOGIN_FAILED, 'Failed login: ghost');

        $row = self::$db->query("SELECT * FROM activity_logs LIMIT 1")->fetch_assoc();
        $this->assertNotNull($row);
        $this->assertNull($row['user_id']);
        $this->assertSame(ActivityLog::EVENT_LOGIN_FAILED, $row['event']);
    }

    #[Test]
    public function it_records_ip_from_remote_addr(): void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.1';

        ActivityLog::logTo(self::$db, ActivityLog::EVENT_LOGOUT, 'User logged out');

        $row = self::$db->query("SELECT ip_address FROM activity_logs LIMIT 1")->fetch_assoc();
        $this->assertSame('192.168.1.1', $row['ip_address']);

        unset($_SERVER['REMOTE_ADDR']);
    }

    #[Test]
    public function it_stores_null_ip_when_remote_addr_is_absent(): void
    {
        unset($_SERVER['REMOTE_ADDR']);

        ActivityLog::logTo(self::$db, ActivityLog::EVENT_LOGOUT, 'User logged out');

        $row = self::$db->query("SELECT ip_address FROM activity_logs LIMIT 1")->fetch_assoc();
        $this->assertNull($row['ip_address']);
    }

    #[Test]
    public function it_stores_description_with_special_characters_as_is(): void
    {
        ActivityLog::logTo(self::$db, ActivityLog::EVENT_LOGIN_FAILED, 'Failed login: <script>alert(1)</script>');

        $row = self::$db->query("SELECT description FROM activity_logs LIMIT 1")->fetch_assoc();
        $this->assertSame('Failed login: <script>alert(1)</script>', $row['description']);
    }

    #[Test]
    public function it_accumulates_multiple_rows(): void
    {
        ActivityLog::logTo(self::$db, ActivityLog::EVENT_LOGIN_SUCCESS, 'first');
        ActivityLog::logTo(self::$db, ActivityLog::EVENT_LOGOUT, 'second');
        ActivityLog::logTo(self::$db, ActivityLog::EVENT_LOGIN_FAILED, 'third');

        $count = self::$db->query("SELECT COUNT(*) AS c FROM activity_logs")->fetch_assoc();
        $this->assertSame(3, (int) $count['c']);
    }

    // --- getAll() ---

    #[Test]
    public function get_all_returns_empty_array_when_no_logs(): void
    {
        $this->assertSame([], $this->model->getAll());
    }

    #[Test]
    public function get_all_resolves_user_name_via_join(): void
    {
        $user = $this->createUser(['first_name' => 'Ana', 'last_name' => 'García']);
        ActivityLog::logTo(self::$db, ActivityLog::EVENT_LOGIN_SUCCESS, 'Login: testuser', $user['id']);

        $logs = $this->model->getAll();
        $this->assertCount(1, $logs);
        $this->assertSame('Ana García', $logs[0]['user_name']);
    }

    #[Test]
    public function get_all_shows_anonimo_when_user_id_is_null(): void
    {
        ActivityLog::logTo(self::$db, ActivityLog::EVENT_LOGIN_FAILED, 'Failed login: ghost');

        $logs = $this->model->getAll();
        $this->assertCount(1, $logs);
        $this->assertSame('Anónimo', $logs[0]['user_name']);
    }

    #[Test]
    public function get_all_orders_by_created_at_desc(): void
    {
        ActivityLog::logTo(self::$db, ActivityLog::EVENT_LOGIN_SUCCESS, 'first');
        // Force a slightly later timestamp for the second row
        self::$db->query("INSERT INTO activity_logs (event, description, created_at) VALUES ('logout', 'second', NOW() + INTERVAL 1 SECOND)");

        $logs = $this->model->getAll();
        $this->assertCount(2, $logs);
        $this->assertSame('second', $logs[0]['description']);
        $this->assertSame('first', $logs[1]['description']);
    }
}
