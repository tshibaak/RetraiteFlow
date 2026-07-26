<?php
namespace App\Models;

class Prevision extends Model
{
    public function __construct()
    {
        parent::__construct('prevision_depense');
    }

    public function byUser(int $userId): array
    {
        return $this->fetchAllWhere(
            "SELECT p.*, c.name AS commission_name
             FROM {$this->table} p
             LEFT JOIN commissions c ON c.id = p.commission_id
             WHERE p.user_id = :user_id
             ORDER BY p.created_at DESC",
            ['user_id' => $userId]
        );
    }

    public function allDetailed(): array
    {
        return $this->fetchAllWhere(
            "SELECT p.*, c.name AS commission_name, u.name AS user_name
             FROM {$this->table} p
             LEFT JOIN commissions c ON c.id = p.commission_id
             INNER JOIN users u ON u.id = p.user_id
             ORDER BY p.created_at DESC"
        );
    }
}
