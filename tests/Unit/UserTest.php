<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Model\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UserTest extends TestCase
{
    private User $users;

    protected function setUp(): void
    {
        parent::setUp();
        $this->users = new User(self::$db);
    }

    // --- getByUsername ---

    #[Test]
    public function it_finds_user_by_username(): void
    {
        $u = $this->createUser(['username' => 'alice']);
        $found = $this->users->getByUsername('alice');
        $this->assertNotNull($found);
        $this->assertSame($u['id'], (int) $found['id']);
    }

    #[Test]
    public function it_returns_null_for_unknown_username(): void
    {
        $this->assertNull($this->users->getByUsername('ghost'));
    }

    // --- getByEmail ---

    #[Test]
    public function it_finds_user_by_email(): void
    {
        $u = $this->createUser(['email' => 'alice@example.com']);
        $found = $this->users->getByEmail('alice@example.com');
        $this->assertNotNull($found);
        $this->assertSame($u['id'], (int) $found['id']);
    }

    #[Test]
    public function it_returns_null_for_unknown_email(): void
    {
        $this->assertNull($this->users->getByEmail('nobody@example.com'));
    }

    // --- getById ---

    #[Test]
    public function it_finds_user_by_id(): void
    {
        $u = $this->createUser();
        $this->assertNotNull($this->users->getById($u['id']));
    }

    #[Test]
    public function it_returns_null_for_unknown_id(): void
    {
        $this->assertNull($this->users->getById(999999));
    }

    // --- create ---

    #[Test]
    public function it_creates_user_with_hashed_password(): void
    {
        $ok = $this->users->create([
            'first_name' => 'Bob',
            'last_name'  => 'Smith',
            'email'      => 'bob@example.com',
            'username'   => 'bob',
            'password'   => 'plaintext123',
            'is_admin'   => 0,
        ]);
        $this->assertTrue($ok);

        $row = self::$db->query("SELECT password FROM users WHERE username='bob'")->fetch_assoc();
        $this->assertNotSame('plaintext123', $row['password']);
        $this->assertTrue(password_verify('plaintext123', $row['password']));
    }

    // --- update ---

    #[Test]
    public function it_updates_user_fields_without_changing_password(): void
    {
        $u = $this->createUser();
        $this->users->update($u['id'], [
            'first_name' => 'Updated',
            'last_name'  => 'Name',
            'email'      => $u['email'],
            'username'   => $u['username'],
            'is_admin'   => 1,
            'password'   => '',
        ]);
        $row = $this->users->getById($u['id']);
        $this->assertSame('Updated', $row['first_name']);
        $this->assertSame(1, (int) $row['is_admin']);
        $this->assertTrue(password_verify($u['password'], $row['password']));
    }

    // --- updatePassword ---

    #[Test]
    public function it_updates_password_and_invalidates_old_one(): void
    {
        $u = $this->createUser(['email' => 'test@example.com', 'password' => 'oldpass']);
        $this->users->updatePassword($u['email'], 'newpass456');
        $row = $this->users->getById($u['id']);
        $this->assertTrue(password_verify('newpass456', $row['password']));
        $this->assertFalse(password_verify('oldpass', $row['password']));
    }

    // --- delete ---

    #[Test]
    public function it_deletes_existing_user(): void
    {
        $u = $this->createUser();
        $this->assertTrue($this->users->delete($u['id']));
        $this->assertNull($this->users->getById($u['id']));
    }

    #[Test]
    public function it_returns_false_when_deleting_unknown_user(): void
    {
        $this->assertFalse($this->users->delete(999999));
    }

    // --- remember token ---

    #[Test]
    public function it_sets_and_retrieves_remember_token(): void
    {
        $u       = $this->createUser();
        $hash    = hash('sha256', bin2hex(random_bytes(32)));
        $expires = date('Y-m-d H:i:s', time() + 3600);

        $this->users->setRememberToken($u['id'], $hash, $expires);
        $found = $this->users->getByRememberToken($hash);
        $this->assertNotNull($found);
        $this->assertSame($u['id'], (int) $found['id']);
    }

    #[Test]
    public function it_clears_remember_token(): void
    {
        $u    = $this->createUser();
        $hash = hash('sha256', 'somerawtoken');
        $this->users->setRememberToken($u['id'], $hash, date('Y-m-d H:i:s', time() + 3600));
        $this->users->clearRememberToken($u['id']);

        $this->assertNull($this->users->getByRememberToken($hash));
        $row = self::$db->query("SELECT remember_token, remember_token_expires FROM users WHERE id={$u['id']}")->fetch_assoc();
        $this->assertNull($row['remember_token']);
        $this->assertNull($row['remember_token_expires']);
    }

    // --- getAll ---

    #[Test]
    public function get_all_returns_all_users(): void
    {
        $this->createUser(['username' => 'a', 'email' => 'a@example.com']);
        $this->createUser(['username' => 'b', 'email' => 'b@example.com']);
        $this->assertCount(2, $this->users->getAll());
    }
}
