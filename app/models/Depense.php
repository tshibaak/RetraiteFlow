<?php
namespace App\Models;

class Depense extends Model
{
    public function __construct()
    {
        parent::__construct('depense_reelles');
    }

    public function byUser(int $userId): array
    {
        return $this->fetchAllWhere(
            "SELECT d.*, c.name AS commission_name
             FROM {$this->table} d
             LEFT JOIN commissions c ON c.id = d.commission_id
             WHERE d.user_id = :user_id
             ORDER BY d.created_at DESC",
            ['user_id' => $userId]
        );
    }

    public function allDetailed(): array
    {
        return $this->fetchAllWhere(
            "SELECT d.*, c.name AS commission_name, u.name AS user_name
             FROM {$this->table} d
             LEFT JOIN commissions c ON c.id = d.commission_id
             INNER JOIN users u ON u.id = d.user_id
             ORDER BY d.created_at DESC"
        );
    }
}
