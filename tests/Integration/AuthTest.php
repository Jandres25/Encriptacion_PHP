<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Core\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AuthTest extends TestCase
{
    private Auth $auth;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auth = new Auth(self::$db);
    }

    // --- verifyCredentials ---

    #[Test]
    public function it_verifies_correct_credentials(): void
    {
        $u = $this->createUser(['username' => 'alice', 'password' => 'secret']);
        $result = $this->auth->verifyCredentials('alice', 'secret');
        $this->assertNotNull($result);
        $this->assertSame($u['id'], (int) $result['id']);
    }

    #[Test]
    public function it_rejects_wrong_password(): void
    {
        $this->createUser(['username' => 'alice', 'password' => 'secret']);
        $this->assertNull($this->auth->verifyCredentials('alice', 'wrong'));
    }

    #[Test]
    public function it_rejects_unknown_user(): void
    {
        $this->assertNull($this->auth->verifyCredentials('ghost', 'secret'));
    }

    // --- issueRememberToken ---

    #[Test]
    public function it_issues_remember_token_and_stores_hash_not_raw(): void
    {
        $u   = $this->createUser();
        $raw = $this->auth->issueRememberToken($u['id']);

        $this->assertNotEmpty($raw);
        $row = self::$db->query("SELECT remember_token, remember_token_expires FROM users WHERE id={$u['id']}")->fetch_assoc();
        $this->assertSame(hash('sha256', $raw), $row['remember_token']);
        $this->assertNotSame($raw, $row['remember_token']);
        $this->assertGreaterThan(time(), strtotime($row['remember_token_expires']));
    }

    // --- consumeRememberToken ---

    #[Test]
    public function it_consumes_valid_remember_token(): void
    {
        $u     = $this->createUser();
        $raw   = $this->auth->issueRememberToken($u['id']);
        $found = $this->auth->consumeRememberToken($raw);
        $this->assertNotNull($found);
        $this->assertSame($u['id'], (int) $found['id']);
    }

    #[Test]
    public function it_rejects_invalid_remember_token(): void
    {
        $this->assertNull($this->auth->consumeRememberToken('deadbeefdeadbeef'));
    }

    #[Test]
    public function it_rejects_expired_remember_token(): void
    {
        $u   = $this->createUser();
        $raw = $this->auth->issueRememberToken($u['id']);
        self::$db->query("UPDATE users SET remember_token_expires = DATE_SUB(NOW(), INTERVAL 2 HOUR) WHERE id={$u['id']}");
        $this->assertNull($this->auth->consumeRememberToken($raw));
    }

    // --- clearRememberToken ---

    #[Test]
    public function it_clears_remember_token_from_db(): void
    {
        $u = $this->createUser();
        $this->auth->issueRememberToken($u['id']);
        $this->auth->clearRememberToken($u['id']);
        $row = self::$db->query("SELECT remember_token, remember_token_expires FROM users WHERE id={$u['id']}")->fetch_assoc();
        $this->assertNull($row['remember_token']);
        $this->assertNull($row['remember_token_expires']);
    }

    // --- createPasswordResetToken ---

    #[Test]
    public function it_creates_reset_token_for_existing_email(): void
    {
        $this->createUser(['email' => 'reset@example.com']);
        $token = $this->auth->createPasswordResetToken('reset@example.com');

        $this->assertNotEmpty($token);
        $tokenHash = hash('sha256', $token);
        $row = self::$db->query("SELECT * FROM password_resets WHERE email='reset@example.com'")->fetch_assoc();
        $this->assertNotNull($row);
        $this->assertSame($tokenHash, $row['token']);
        $this->assertSame(0, (int) $row['used']);
        $this->assertGreaterThan(time(), strtotime($row['expires_at']));
    }

    #[Test]
    public function it_returns_null_for_reset_with_unknown_email(): void
    {
        $this->assertNull($this->auth->createPasswordResetToken('nobody@example.com'));
    }

    // --- consumeResetToken ---

    #[Test]
    public function it_consumes_valid_reset_token_and_changes_password(): void
    {
        $u     = $this->createUser(['email' => 'reset@example.com', 'password' => 'oldpass']);
        $token = $this->auth->createPasswordResetToken('reset@example.com');
        $email = $this->auth->consumeResetToken($token, 'newpass');

        $this->assertSame('reset@example.com', $email);

        $row = self::$db->query("SELECT password FROM users WHERE id={$u['id']}")->fetch_assoc();
        $this->assertTrue(password_verify('newpass', $row['password']));

        $tokenHash = hash('sha256', $token);
        $reset = self::$db->query("SELECT used FROM password_resets WHERE token='{$tokenHash}'")->fetch_assoc();
        $this->assertSame(1, (int) $reset['used']);
    }

    #[Test]
    public function it_rejects_expired_reset_token(): void
    {
        $this->createUser(['email' => 'reset@example.com']);
        $token     = $this->auth->createPasswordResetToken('reset@example.com');
        $tokenHash = hash('sha256', $token);
        self::$db->query("UPDATE password_resets SET expires_at = DATE_SUB(NOW(), INTERVAL 2 HOUR) WHERE token='{$tokenHash}'");
        $this->assertNull($this->auth->consumeResetToken($token, 'whatever'));
    }

    #[Test]
    public function it_rejects_already_used_reset_token(): void
    {
        $this->createUser(['email' => 'reset@example.com']);
        $token = $this->auth->createPasswordResetToken('reset@example.com');
        $this->auth->consumeResetToken($token, 'first');
        $this->assertNull($this->auth->consumeResetToken($token, 'second'));
    }

    #[Test]
    public function it_rejects_unknown_reset_token(): void
    {
        $this->assertNull($this->auth->consumeResetToken('not-a-real-token', 'x'));
    }

    // --- lockout (LOGIN_LOCKOUT_ENABLED=false in .env.testing, so we force-enable via $_ENV) ---

    #[Test]
    public function lockout_disabled_always_returns_zero(): void
    {
        // Already disabled by .env.testing; verify the contract explicitly.
        $this->assertSame(0, $this->auth->lockedSecondsRemaining('anyuser'));
    }

    #[Test]
    public function user_exists_returns_true_for_known_username(): void
    {
        $this->createUser(['username' => 'knownuser']);
        $this->assertTrue($this->auth->userExists('knownuser'));
    }

    #[Test]
    public function user_exists_returns_false_for_unknown_username(): void
    {
        $this->assertFalse($this->auth->userExists('ghost'));
    }

    #[Test]
    public function clear_failed_attempts_removes_both_email_and_username(): void
    {
        $_ENV['LOGIN_LOCKOUT_ENABLED'] = 'true';
        $_ENV['LOGIN_MAX_ATTEMPTS']    = '2';
        $_ENV['LOGIN_LOCKOUT_MINUTES'] = '15';

        try {
            $u = $this->createUser(['email' => 'lock@example.com', 'username' => 'lockuser']);

            // Simulate enough failures on both identifiers to create rows.
            for ($i = 0; $i < 2; $i++) {
                self::$db->query("INSERT INTO login_attempts (identifier, attempts, last_attempt)
                    VALUES ('lock@example.com', 1, NOW()), ('lockuser', 1, NOW())
                    ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = NOW()");
            }

            $this->auth->clearFailedAttempts('lock@example.com');
            $this->auth->clearFailedAttempts($u['username']);

            $this->assertSame(0, (int) self::$db->query("SELECT COUNT(*) c FROM login_attempts WHERE identifier IN ('lock@example.com','lockuser')")->fetch_assoc()['c']);
        } finally {
            $_ENV['LOGIN_LOCKOUT_ENABLED'] = 'false';
            unset($_ENV['LOGIN_MAX_ATTEMPTS'], $_ENV['LOGIN_LOCKOUT_MINUTES']);
        }
    }

    #[Test]
    public function consume_reset_token_clears_lockout_for_email_and_username(): void
    {
        $this->createUser(['email' => 'lock2@example.com', 'username' => 'lockuser2']);
        $token = $this->auth->createPasswordResetToken('lock2@example.com');

        // Insert lockout rows manually.
        self::$db->query("INSERT INTO login_attempts (identifier, attempts, locked_until, last_attempt)
            VALUES ('lock2@example.com', 5, DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW()),
                   ('lockuser2', 5, DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW())");

        $this->auth->consumeResetToken($token, 'newpassword');

        $count = (int) self::$db->query(
            "SELECT COUNT(*) c FROM login_attempts WHERE identifier IN ('lock2@example.com','lockuser2')"
        )->fetch_assoc()['c'];
        $this->assertSame(0, $count);
    }
}
