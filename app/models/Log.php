<?php
namespace App\Models;

class Log extends Model
{
    public function __construct()
    {
        parent::__construct('logs');
    }

    public function create(array $datas): bool
    {
        return $this->insert($datas);
    }

    public function recent(int $limit = 80, ?int $userId = null): array
    {
        if ($userId !== null) {
            return $this->fetchAllWhere(
                "SELECT l.*, u.name AS user_name
                 FROM {$this->table} l
                 INNER JOIN users u ON u.id = l.user_id
                 WHERE l.user_id = :user_id
                 ORDER BY l.created_at DESC, l.id DESC
                 LIMIT $limit",
                ['user_id' => $userId]
            );
        }

        return $this->fetchAllWhere(
            "SELECT l.*, u.name AS user_name
             FROM {$this->table} l
             INNER JOIN users u ON u.id = l.user_id
             ORDER BY l.created_at DESC, l.id DESC
             LIMIT $limit"
        );
    }

    public function byRole(string $roleName, int $limit = 80): array
    {
        return $this->fetchAllWhere(
            "SELECT l.*, u.name AS user_name, r.name AS role_name
             FROM {$this->table} l
             INNER JOIN users u ON u.id = l.user_id
             INNER JOIN roles r ON r.id = u.role_id
             WHERE r.name = :role
             ORDER BY l.created_at DESC, l.id DESC
             LIMIT $limit",
            ['role' => $roleName]
        );
    }
}
