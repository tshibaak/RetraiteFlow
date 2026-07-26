<?php
namespace App\Controllers;

use App\Models\Depense;
use App\Models\Local;
use App\Models\Log;
use App\Models\Paiement;
use App\Models\Participant;
use App\Models\Prevision;
use App\View;

class CordonController extends Controller
{
    public function index(): void
    {
        $user = $this->requireAuth(['coordination', 'coordon']);

        $participant = new Participant();
        $local = new Local();
        $paiement = new Paiement();
        $depense = new Depense();
        $prevision = new Prevision();

        $totalInputs = $paiement->sumConfirmed();
        $totalActuals = $depense->sum('budget_depense_rel');
        $totalForecasts = $prevision->sum('budget');

        View::view('cordon', [
            'nom_enc' => $user->name,
            'kpi_participants' => $participant->count(),
            'kpi_dortoirs' => $local->countByCategory('dortoir'),
            'kpi_ateliers' => $local->countByCategory('atelier'),
            'kpi_finance' => $totalInputs,
            'total_actuals' => $totalActuals,
            'total_forecasts' => $totalForecasts,
            'participants_loges' => $participant->countAssigned('dortoir_id'),
            'participants_atelier' => $participant->countAssigned('atelier_id'),
            'count_solvables' => $participant->countByGroupe('solvable'),
            'count_accredites' => $participant->countByGroupe('accredited'),
            'count_sociaux' => $participant->countByGroupe('social_case'),
            'count_confirmes' => $paiement->countByStatut('confirmed'),
            'count_attente' => $paiement->countByStatut('pending'),
            'count_deconfirmes' => $paiement->countByStatut('rejected'),
            'activity_logs' => (new Log())->recent(100),
        ]);
    }
}
