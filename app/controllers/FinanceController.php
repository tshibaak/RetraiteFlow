<?php
namespace App\Controllers;

use App\Models\Commission;
use App\Models\Depense;
use App\Models\Log;
use App\Models\Paiement;
use App\Models\Participant;
use App\Models\Prevision;
use App\View;

class FinanceController extends Controller
{
    protected Depense $depense;
    protected Prevision $prevision;
    protected Paiement $paiement;
    protected Participant $participant;
    protected Commission $commission;
    protected LogController $logger;

    public function __construct()
    {
        $this->depense = new Depense();
        $this->prevision = new Prevision();
        $this->paiement = new Paiement();
        $this->participant = new Participant();
        $this->commission = new Commission();
        $this->logger = new LogController();
    }

    public function index(): void
    {
        $user = $this->requireAuth(['finance']);
        $userId = (int) $user->id;

        $totalInputs = $this->paiement->sumConfirmed();
        $totalActuals = $this->depense->sum('budget_depense_rel', 'user_id = :uid', ['uid' => $userId]);
        $totalForecasts = $this->prevision->sum('budget', 'user_id = :uid', ['uid' => $userId]);

        $participants = $this->participant->allDetailed();
        $confirmed = $this->paiement->countByStatut('confirmed');
        $pending = $this->paiement->countByStatut('pending');
        $rejected = $this->paiement->countByStatut('rejected');

        View::view('finance', [
            'nom_enc' => $user->name,
            'total_inputs' => $totalInputs,
            'total_actuals' => $totalActuals,
            'total_forecasts' => $totalForecasts,
            'solde' => $totalInputs - $totalActuals,
            'remaining_budget' => $totalForecasts - $totalActuals,
            'total_participants' => count($participants),
            'confirmed_participants' => $confirmed,
            'pending_participants' => $pending,
            'rejected_participants' => $rejected,
            'all_forecasts' => $this->prevision->byUser($userId),
            'all_actuals' => $this->depense->byUser($userId),
            'finance_participants' => $participants,
            'activity_logs' => (new Log())->byRole('encadreur', 80),
        ]);
    }

    public function handle(): void
    {
        $user = $this->requireAuthJson(['finance']);
        $action = self::sanitize($_POST['action'] ?? '');

        try {
            switch ($action) {
                case 'add_forecast':
                    $this->addForecast($user);
                    break;
                case 'delete_forecast':
                    $this->deleteForecast($user, (int) ($_POST['id_forecast'] ?? 0));
                    break;
                case 'add_actual':
                    $this->addActual($user);
                    break;
                case 'delete_actual':
                    $this->deleteActual($user, (int) ($_POST['id_actual'] ?? 0));
                    break;
                case 'set_participant_status':
                    $this->setParticipantStatus($user);
                    break;
                case 'add_input':
                case 'delete_input':
                    self::status(400)->json([
                        'status' => false,
                        'message' => 'Les entrées complémentaires sont remplacées par les paiements confirmés.',
                    ]);
                    break;
                default:
                    self::status(400)->json(['status' => false, 'message' => 'Action invalide']);
            }
        } catch (\Throwable $e) {
            self::status(500)->json(['status' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    private function addForecast(object $user): void
    {
        $commissionLabel = self::sanitize($_POST['commission'] ?? '');
        $budget = filter_input(INPUT_POST, 'budget', FILTER_VALIDATE_FLOAT);

        if ($commissionLabel === '' || $budget === false || $budget <= 0) {
            self::status(400)->json(['status' => false, 'message' => 'Champs invalides']);
        }

        $commissionId = $this->commission->resolveId($commissionLabel);
        $this->prevision->insert([
            'user_id' => (int) $user->id,
            'commission_id' => $commissionId,
            'budget' => $budget,
        ]);

        $this->logger->store((int) $user->id, 'enregistrement', "Prévision {$commissionLabel}: {$budget}");
        self::json(['status' => true, 'message' => 'Prévision ajoutée avec succès']);
    }

    private function deleteForecast(object $user, int $id): void
    {
        $row = $this->prevision->find($id);
        if (!$row || (int) $row['user_id'] !== (int) $user->id) {
            self::status(400)->json(['status' => false, 'message' => 'Prévision introuvable']);
        }

        $this->prevision->delete($id);
        $this->logger->store((int) $user->id, 'suppression', 'Suppression d\'une prévision');
        self::json(['status' => true, 'message' => 'Prévision supprimée avec succès']);
    }

    private function addActual(object $user): void
    {
        $commissionLabel = self::sanitize($_POST['commission'] ?? '');
        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);

        if ($commissionLabel === '' || $amount === false || $amount <= 0) {
            self::status(400)->json(['status' => false, 'message' => 'Champs invalides']);
        }

        $commissionId = $this->commission->resolveId($commissionLabel);
        $this->depense->insert([
            'user_id' => (int) $user->id,
            'commission_id' => $commissionId,
            'budget_depense_rel' => $amount,
        ]);

        $this->logger->store((int) $user->id, 'achat', "Dépense réelle {$commissionLabel}: {$amount}");
        self::json(['status' => true, 'message' => 'Dépense ajoutée avec succès']);
    }

    private function deleteActual(object $user, int $id): void
    {
        $row = $this->depense->find($id);
        if (!$row || (int) $row['user_id'] !== (int) $user->id) {
            self::status(400)->json(['status' => false, 'message' => 'Dépense introuvable']);
        }

        $this->depense->delete($id);
        $this->logger->store((int) $user->id, 'suppression', 'Suppression d\'une dépense réelle');
        self::json(['status' => true, 'message' => 'Dépense supprimée avec succès']);
    }

    private function setParticipantStatus(object $user): void
    {
        $participantId = filter_input(INPUT_POST, 'id_participant', FILTER_VALIDATE_INT);
        $status = self::sanitize($_POST['status'] ?? '');

        $map = [
            'confirme' => 'confirmed',
            'confirmed' => 'confirmed',
            'deconfirme' => 'rejected',
            'rejected' => 'rejected',
            'pending' => 'pending',
            'en_attente' => 'pending',
        ];

        if (!$participantId || !isset($map[$status])) {
            self::status(400)->json(['status' => false, 'message' => 'Paramètres invalides']);
        }

        $participant = $this->participant->find($participantId);
        if (!$participant) {
            self::status(400)->json(['status' => false, 'message' => 'Participant introuvable']);
        }

        $this->paiement->setStatut($participantId, $map[$status], (int) $user->id);
        $label = $map[$status] === 'confirmed' ? 'Confirmation' : 'Déconfirmation';
        $this->logger->store(
            (int) $user->id,
            'validation',
            "{$label} du paiement de {$participant['name']}"
        );

        self::json(['status' => true, 'message' => 'Statut mis à jour']);
    }
}
