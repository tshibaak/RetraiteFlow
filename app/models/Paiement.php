<?php
namespace App\Models;

class Paiement extends Model
{
    public function __construct()
    {
        parent::__construct('paiements');
    }

    public function latestForParticipant(int $participantId)
    {
        return $this->fetchOne(
            "SELECT * FROM {$this->table}
             WHERE participant_id = :participant_id
             ORDER BY created_at DESC, id DESC
             LIMIT 1",
            ['participant_id' => $participantId]
        );
    }

    public function upsertForParticipant(int $participantId, float $amount, int $validatorId, string $mode = 'cash'): int
    {
        $existing = $this->latestForParticipant($participantId);

        if ($existing) {
            $this->updateById((int) $existing['id'], [
                'amount' => $amount,
                'mode' => $mode,
                'validator_id' => $validatorId,
            ]);
            return (int) $existing['id'];
        }

        $this->insert([
            'participant_id' => $participantId,
            'amount' => $amount,
            'mode' => $mode,
            'statut' => 'pending',
            'validator_id' => $validatorId,
        ]);

        return $this->lastInsertId();
    }

    public function setStatut(int $participantId, string $statut, int $validatorId): bool
    {
        $allowed = ['pending', 'confirmed', 'rejected'];
        if (!in_array($statut, $allowed, true)) {
            return false;
        }

        $existing = $this->latestForParticipant($participantId);
        if (!$existing) {
            $this->insert([
                'participant_id' => $participantId,
                'amount' => 0,
                'mode' => 'cash',
                'statut' => $statut,
                'validator_id' => $validatorId,
            ]);
            return true;
        }

        return $this->updateById((int) $existing['id'], [
            'statut' => $statut,
            'validator_id' => $validatorId,
        ]);
    }

    public function sumConfirmed(): float
    {
        return $this->sum('amount', "statut = 'confirmed'");
    }

    public function countByStatut(string $statut): int
    {
        return $this->count('statut = :statut', ['statut' => $statut]);
    }
}
