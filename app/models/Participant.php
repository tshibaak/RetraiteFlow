<?php
namespace App\Models;

class Participant extends Model
{
    public function __construct()
    {
        parent::__construct('participants');
    }

    public function byUser(int $userId): array
    {
        return $this->fetchAllWhere(
            "SELECT p.*,
                    g.name AS groupe_name,
                    c.name AS commission_name,
                    COALESCE(pay.amount, 0) AS amount,
                    COALESCE(pay.statut, 'pending') AS paiement_statut,
                    pay.id AS paiement_id
             FROM {$this->table} p
             INNER JOIN groupes g ON g.id = p.groupe_id
             LEFT JOIN commissions c ON c.id = p.commission_id
             LEFT JOIN paiements pay ON pay.id = (
                SELECT p2.id FROM paiements p2
                WHERE p2.participant_id = p.id
                ORDER BY p2.created_at DESC, p2.id DESC
                LIMIT 1
             )
             WHERE p.user_id = :user_id
             ORDER BY p.name ASC",
            ['user_id' => $userId]
        );
    }

    public function allDetailed(): array
    {
        return $this->fetchAllWhere(
            "SELECT p.*,
                    g.name AS groupe_name,
                    c.name AS commission_name,
                    u.name AS encadreur_name,
                    COALESCE(pay.amount, 0) AS amount,
                    COALESCE(pay.statut, 'pending') AS paiement_statut,
                    pay.id AS paiement_id,
                    v.name AS validator_name
             FROM {$this->table} p
             INNER JOIN groupes g ON g.id = p.groupe_id
             LEFT JOIN commissions c ON c.id = p.commission_id
             INNER JOIN users u ON u.id = p.user_id
             LEFT JOIN paiements pay ON pay.id = (
                SELECT p2.id FROM paiements p2
                WHERE p2.participant_id = p.id
                ORDER BY p2.created_at DESC, p2.id DESC
                LIMIT 1
             )
             LEFT JOIN users v ON v.id = pay.validator_id
             ORDER BY p.created_at DESC, p.name ASC"
        );
    }

    public function belongsToUser(int $id, int $userId): bool
    {
        $row = $this->fetchOne(
            "SELECT id FROM {$this->table} WHERE id = :id AND user_id = :user_id",
            ['id' => $id, 'user_id' => $userId]
        );
        return (bool) $row;
    }

    public function countByGroupe(string $groupeName, ?int $userId = null): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} p
                INNER JOIN groupes g ON g.id = p.groupe_id
                WHERE g.name = :groupe";
        $params = ['groupe' => $groupeName];

        if ($userId !== null) {
            $sql .= ' AND p.user_id = :user_id';
            $params['user_id'] = $userId;
        }

        $stmt = self::$connection->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function countAssigned(string $column): int
    {
        return $this->count("$column IS NOT NULL");
    }
}
