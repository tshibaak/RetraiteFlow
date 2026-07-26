<?php
namespace App\Controllers;

use App\Models\Participant;
use App\View;

class DisciplineController extends Controller
{
    public function index(): void
    {
        $user = $this->requireAuth(['discipline']);

        $participants = (new Participant())->allDetailed();

        View::view('discipline', [
            'nom_enc' => $user->name,
            'count_entries' => 0,
            'count_exits' => 0,
            'participants' => $participants,
        ]);
    }

    public function handle(): void
    {
        $this->requireAuthJson(['discipline']);
        self::json([
            'status' => true,
            'message' => 'Le suivi présence est géré côté client (aucune table dédiée dans le schéma actuel).',
            'logs' => [],
        ]);
    }
}
