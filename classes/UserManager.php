<?php
require_once __DIR__.'/JsonFile.php';
require_once __DIR__.'/User.php';

class UserManager {
    private JsonFile $store;

    public function __construct(string $filePath) {
        $this->store = new JsonFile($filePath);
    }

    public function all(): array { return $this->store->read(); }

    public function findByUsername(string $username): ?array {
        foreach ($this->all() as $u) {
            if (strcasecmp($u['username'], $username) === 0) return $u;
        }
        return null;
    }

    public function findById(int $id): ?array {
        foreach ($this->all() as $u) { if ($u['id'] === $id) return $u; }
        return null;
    }

    public function nextId(): int {
        $max = 0; foreach ($this->all() as $u) { if ($u['id'] > $max) $max = $u['id']; }
        return $max + 1;
    }

    public function add(User $user): void {
        $users = $this->all();
        $users[] = ['id'=>$user->id, 'username'=>$user->username, 'passwordHash'=>$user->passwordHash];
        $this->store->write($users);
    }

    public function updatePassword(int $userId, string $newPassword): void {
        $users = $this->all();
        foreach ($users as &$u) {
            if ($u['id'] === $userId) { $u['passwordHash'] = password_hash($newPassword, PASSWORD_DEFAULT); break; }
        }
        $this->store->write($users);
    }

    public function delete(int $userId): void {
        $users = array_values(array_filter($this->all(), fn($u) => $u['id'] !== $userId));
        $this->store->write($users);
    }
}
