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
        $row = self::$db->query("SELECT * FROM password_resets WHERE email='reset@example.com'")->fetch_assoc();
        $this->assertNotNull($row);
        $this->assertSame($token, $row['token']);
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

        $reset = self::$db->query("SELECT used FROM password_resets WHERE token='{$token}'")->fetch_assoc();
        $this->assertSame(1, (int) $reset['used']);
    }

    #[Test]
    public function it_rejects_expired_reset_token(): void
    {
        $this->createUser(['email' => 'reset@example.com']);
        $token = $this->auth->createPasswordResetToken('reset@example.com');
        self::$db->query("UPDATE password_resets SET expires_at = DATE_SUB(NOW(), INTERVAL 2 HOUR) WHERE token='{$token}'");
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
}
