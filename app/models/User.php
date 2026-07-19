<?php
namespace App\Models;

use App\Models\Model;

class User extends Model
{
    public function __construct()
    {
        parent::__construct("users");
    }

    public function findByEmail(string $email,int $fecthMode = \PDO::FETCH_ASSOC)
    {
        $stmt = self::$connection->prepare("SELECT users.*, roles.name AS role_name
                    FROM {$this->table}
                    INNER JOIN roles ON users.role_id = roles.id
                    WHERE users.email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch($fecthMode);
    }
}
