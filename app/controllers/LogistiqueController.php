<?php
namespace App\Controllers;

use App\Models\Category;
use App\Models\Local;
use App\Models\Participant;
use App\View;

class LogistiqueController extends Controller
{
    protected Local $local;
    protected Category $category;
    protected Participant $participant;
    protected LogController $logger;

    public function __construct()
    {
        $this->local = new Local();
        $this->category = new Category();
        $this->participant = new Participant();
        $this->logger = new LogController();
    }

    public function index(): void
    {
        $user = $this->requireAuth(['logistique']);

        $dortoirs = $this->local->byCategory('dortoir');
        $ateliers = $this->local->byCategory('atelier');

        View::view('logistique', [
            'nom_enc' => $user->name,
            'count_participants' => $this->participant->count(),
            'count_dortoirs' => count($dortoirs),
            'count_ateliers' => count($ateliers),
            'all_dortoirs' => $dortoirs,
            'all_ateliers' => $ateliers,
        ]);
    }

    public function handle(): void
    {
        $user = $this->requireAuthJson(['logistique']);
        $action = self::sanitize($_POST['action'] ?? '');

        try {
            switch ($action) {
                case 'add_dortoir':
                    $this->addLocal($user, 'dortoir', true);
                    break;
                case 'delete_dortoir':
                    $this->deleteLocal($user, (int) ($_POST['id_dortoir'] ?? 0), 'dortoir');
                    break;
                case 'add_atelier':
                    $this->addLocal($user, 'atelier', false);
                    break;
                case 'delete_atelier':
                    $this->deleteLocal($user, (int) ($_POST['id_atelier'] ?? 0), 'atelier');
                    break;
                default:
                    self::status(400)->json(['status' => false, 'message' => 'Action invalide']);
            }
        } catch (\Throwable $e) {
            self::status(500)->json(['status' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    private function addLocal(object $user, string $categoryName, bool $requireSexe): void
    {
        $name = self::sanitize($_POST['nom'] ?? '');
        $ageMin = filter_input(INPUT_POST, 'age_min', FILTER_VALIDATE_INT);
        $ageMax = filter_input(INPUT_POST, 'age_max', FILTER_VALIDATE_INT);
        $capacity = filter_input(INPUT_POST, 'capacite', FILTER_VALIDATE_INT);
        $sexe = $requireSexe
            ? self::sanitize($_POST['sexe'] ?? '')
            : 'Mixte';

        if ($name === '' || $ageMin === false || $ageMax === false || $capacity === false || ($requireSexe && $sexe === '')) {
            self::status(400)->json(['status' => false, 'message' => 'Champs invalides']);
        }

        $categoryId = $this->category->idByName($categoryName);
        if (!$categoryId) {
            self::status(400)->json(['status' => false, 'message' => 'Catégorie introuvable']);
        }

        $id = $this->local->createLocal((int) $user->id, $categoryId, [
            'name' => $name,
            'sexe' => $sexe,
            'age_min' => $ageMin,
            'age_max' => $ageMax,
            'capacity' => $capacity,
        ]);

        $this->local->autoAssign();
        $this->logger->store((int) $user->id, 'enregistrement', "Création {$categoryName}: {$name}");

        self::json([
            'status' => true,
            'message' => ucfirst($categoryName) . ' créé avec succès',
            'id' => $id,
        ]);
    }

    private function deleteLocal(object $user, int $id, string $label): void
    {
        if ($id <= 0 || !$this->local->belongsToUser($id, (int) $user->id)) {
            self::status(400)->json(['status' => false, 'message' => ucfirst($label) . ' introuvable']);
        }

        $this->local->delete($id);
        $this->local->autoAssign();
        $this->logger->store((int) $user->id, 'suppression', "Suppression d'un {$label}");

        self::json(['status' => true, 'message' => ucfirst($label) . ' supprimé avec succès']);
    }
}
