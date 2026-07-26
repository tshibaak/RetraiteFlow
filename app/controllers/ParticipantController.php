<?php
namespace App\Controllers;

use App\Models\Commission;
use App\Models\Groupe;
use App\Models\Local;
use App\Models\Paiement;
use App\Models\Participant;
use App\View;

class ParticipantController extends Controller
{
    protected Participant $participant;
    protected Groupe $groupe;
    protected Commission $commission;
    protected Paiement $paiement;
    protected Local $local;
    protected LogController $logger;

    public function __construct()
    {
        $this->participant = new Participant();
        $this->groupe = new Groupe();
        $this->commission = new Commission();
        $this->paiement = new Paiement();
        $this->local = new Local();
        $this->logger = new LogController();
    }

    public function index(): void
    {
        $user = $this->requireAuth(['encadreur']);
        $userId = (int) $user->id;

        $participants = $this->participant->byUser($userId);
        $groupe = new Groupe();

        View::view('encadreur', [
            'nom_enc' => $user->name,
            'participants' => array_map(function ($p) use ($groupe) {
                $p['groupe_label'] = $groupe->label($p['groupe_name'] ?? '');
                return $p;
            }, $participants),
            'stats_sum_solvable' => $this->participant->countByGroupe('solvable', $userId),
            'stats_sum_accredite' => $this->participant->countByGroupe('accredited', $userId),
            'stats_sum_social' => $this->participant->countByGroupe('social_case', $userId),
            'stats_total_paiment' => array_sum(array_map(fn ($p) => (float) $p['amount'], $participants)),
            'activity_logs' => (new \App\Models\Log())->recent(50, $userId),
        ]);
    }

    public function store(): void
    {
        $user = $this->requireAuth(['encadreur']);
        $userId = (int) $user->id;
        $action = self::sanitize($_POST['action'] ?? 'save');

        if ($action === 'delete') {
            $this->deleteParticipant($user);
            return;
        }

        $participantId = filter_input(INPUT_POST, 'participant_id', FILTER_VALIDATE_INT) ?: null;
        $name = self::sanitize($_POST['nom'] ?? '');
        $sexe = self::sanitize($_POST['sexe'] ?? '');
        $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
        $groupeLabel = self::sanitize($_POST['groupe'] ?? '');
        $commissionLabel = self::sanitize($_POST['commission'] ?? '');
        $phone = self::sanitize($_POST['telephone'] ?? '');
        $amount = filter_input(INPUT_POST, 'montant', FILTER_VALIDATE_FLOAT);
        $days = filter_input(INPUT_POST, 'jours', FILTER_VALIDATE_INT);

        if ($name === '' || $sexe === '' || $age === false || $groupeLabel === '' || $commissionLabel === '' || $phone === '' || $days === false) {
            $this->message('Certains champs sont invalides ou vides.');
            $this->redirect('/encadreur');
        }

        if (!preg_match('/^[0-9+\-\s]+$/', $phone)) {
            $this->message('Téléphone invalide.');
            $this->redirect('/encadreur');
        }

        $sexeNorm = $this->normalizeSexe($sexe);
        $groupeId = $this->groupe->resolveId($groupeLabel);
        if (!$groupeId || !$sexeNorm) {
            $this->message('Groupe ou sexe invalide.');
            $this->redirect('/encadreur');
        }

        $groupeRow = $this->groupe->find($groupeId);
        $commissionId = $this->commission->resolveId($commissionLabel);

        if (($groupeRow['name'] ?? '') === 'social_case') {
            $amount = 0;
        } elseif ($amount === false || $amount === null || $amount < 0) {
            $this->message('Montant invalide.');
            $this->redirect('/encadreur');
        }

        $payload = [
            'user_id' => $userId,
            'name' => $name,
            'sexe' => $sexeNorm,
            'age' => $age,
            'phone' => $phone,
            'groupe_id' => $groupeId,
            'commission_id' => $commissionId,
            'days' => $days,
        ];

        if ($participantId) {
            if (!$this->participant->belongsToUser($participantId, $userId)) {
                $this->message('Participant introuvable.');
                $this->redirect('/encadreur');
            }
            unset($payload['user_id']);
            $this->participant->updateById($participantId, $payload);
            $this->paiement->upsertForParticipant($participantId, (float) $amount, $userId);
            $this->local->autoAssign();
            $this->logger->store($userId, 'modification', "Modification du participant {$name}");
            $this->success('Participant modifié !');
        } else {
            $this->participant->insert($payload);
            $newId = $this->participant->lastInsertId();
            $this->paiement->upsertForParticipant($newId, (float) $amount, $userId);
            $this->local->autoAssign();
            $this->logger->store($userId, 'enregistrement', "Enregistrement du participant {$name}");
            $this->success('Inscription réussie !');
        }

        $this->redirect('/encadreur');
    }

    private function deleteParticipant(object $user): void
    {
        $userId = (int) $user->id;
        $participantId = filter_input(INPUT_POST, 'participant_id', FILTER_VALIDATE_INT);
        if (!$participantId || !$this->participant->belongsToUser($participantId, $userId)) {
            $this->message('Participant introuvable.');
            $this->redirect('/encadreur');
        }

        $row = $this->participant->find($participantId);
        $this->participant->delete($participantId);
        $this->local->autoAssign();
        $this->logger->store($userId, 'suppression', 'Suppression du participant ' . ($row['name'] ?? ''));
        $this->success('Participant supprimé !');
        $this->redirect('/encadreur');
    }

    private function normalizeSexe(string $sexe): ?string
    {
        $value = mb_strtoupper(trim($sexe), 'UTF-8');
        return match ($value) {
            'MASCULIN', 'M' => 'Masculin',
            'FÉMININ', 'FEMININ', 'F' => 'Féminin',
            default => null,
        };
    }
}
