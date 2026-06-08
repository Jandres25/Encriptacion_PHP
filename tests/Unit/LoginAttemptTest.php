<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Model\LoginAttempt;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LoginAttemptTest extends TestCase
{
    private LoginAttempt $model;
    private const MAX   = 3;
    private const MINS  = 15;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new LoginAttempt(self::$db);
    }

    #[Test]
    public function first_failure_creates_row_with_no_lock(): void
    {
        $this->model->registerFailure('alice', self::MAX, self::MINS);

        $row = self::$db->query("SELECT attempts, locked_until FROM login_attempts WHERE identifier='alice'")->fetch_assoc();
        $this->assertSame(1, (int) $row['attempts']);
        $this->assertNull($row['locked_until']);
        $this->assertSame(0, $this->model->lockedSecondsRemaining('alice'));
    }

    #[Test]
    public function failures_below_threshold_do_not_lock(): void
    {
        for ($i = 0; $i < self::MAX - 1; $i++) {
            $this->model->registerFailure('alice', self::MAX, self::MINS);
        }
        $this->assertSame(0, $this->model->lockedSecondsRemaining('alice'));
    }

    #[Test]
    public function nth_failure_triggers_lock(): void
    {
        for ($i = 0; $i < self::MAX; $i++) {
            $this->model->registerFailure('alice', self::MAX, self::MINS);
        }
        $secs = $this->model->lockedSecondsRemaining('alice');
        $this->assertGreaterThan(0, $secs);
        $this->assertLessThanOrEqual(self::MINS * 60, $secs);
    }

    #[Test]
    public function clear_removes_row_and_returns_zero_remaining(): void
    {
        for ($i = 0; $i < self::MAX; $i++) {
            $this->model->registerFailure('alice', self::MAX, self::MINS);
        }
        $this->model->clear('alice');
        $this->assertSame(0, $this->model->lockedSecondsRemaining('alice'));
    }

    #[Test]
    public function identifier_is_case_and_whitespace_normalized(): void
    {
        $this->model->registerFailure('Alice', self::MAX, self::MINS);
        $this->model->registerFailure('  alice  ', self::MAX, self::MINS);

        $row = self::$db->query("SELECT attempts FROM login_attempts WHERE identifier='alice'")->fetch_assoc();
        $this->assertSame(2, (int) $row['attempts']);
    }

    #[Test]
    public function expired_lock_returns_zero_remaining(): void
    {
        for ($i = 0; $i < self::MAX; $i++) {
            $this->model->registerFailure('alice', self::MAX, self::MINS);
        }
        // Force expiry by setting locked_until in the past.
        self::$db->query("UPDATE login_attempts SET locked_until = DATE_SUB(NOW(), INTERVAL 1 MINUTE) WHERE identifier='alice'");
        $this->assertSame(0, $this->model->lockedSecondsRemaining('alice'));
    }

    #[Test]
    public function no_row_returns_zero_remaining(): void
    {
        $this->assertSame(0, $this->model->lockedSecondsRemaining('nobody'));
    }
}
