<?php
require_once __DIR__ . "/BaseORM.php";

class UserModel extends BaseORM
{
    protected $table = "players";
    protected $primaryKey = "player_id";

    public function findByUsername(string $username): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function updateLastLogin(int $userId): bool
    {
        $sql = "UPDATE {$this->table} SET last_login = CURRENT_TIMESTAMP WHERE player_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$userId]);
    }
}
