<?php
namespace App\Models;

use PDO;

class User extends Model
{
    public function __construct()
    {
        parent::__construct('users');
    }

    public function findByEmail(string $email, int $fetchMode = PDO::FETCH_ASSOC)
    {
        return $this->fetchOne(
            "SELECT users.*, roles.name AS role_name
             FROM {$this->table}
             INNER JOIN roles ON users.role_id = roles.id
             WHERE users.email = :email",
            ['email' => $email],
            $fetchMode
        );
    }

    public function findByIdWithRole(int $id, int $fetchMode = PDO::FETCH_ASSOC)
    {
        return $this->fetchOne(
            "SELECT users.*, roles.name AS role_name
             FROM {$this->table}
             INNER JOIN roles ON users.role_id = roles.id
             WHERE users.id = :id",
            ['id' => $id],
            $fetchMode
        );
    }

    public function allWithRoles(): array
    {
        return $this->fetchAllWhere(
            "SELECT users.*, roles.name AS role_name
             FROM {$this->table}
             INNER JOIN roles ON users.role_id = roles.id
             ORDER BY users.name ASC"
        );
    }
}
