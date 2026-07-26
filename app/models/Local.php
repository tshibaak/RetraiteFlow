<?php
namespace App\Models;

class Local extends Model
{
    public function __construct()
    {
        parent::__construct('locaux');
    }

    public function byCategory(string $categoryName): array
    {
        return $this->fetchAllWhere(
            "SELECT l.*, c.name AS category_name,
                    (SELECT COUNT(*) FROM participants p WHERE p.dortoir_id = l.id OR p.atelier_id = l.id) AS occupants
             FROM {$this->table} l
             INNER JOIN categories c ON c.id = l.category_id
             WHERE c.name = :category
             ORDER BY l.name ASC",
            ['category' => $categoryName]
        );
    }

    public function countByCategory(string $categoryName): int
    {
        $stmt = self::$connection->prepare(
            "SELECT COUNT(*) FROM {$this->table} l
             INNER JOIN categories c ON c.id = l.category_id
             WHERE c.name = :category"
        );
        $stmt->execute(['category' => $categoryName]);
        return (int) $stmt->fetchColumn();
    }

    public function createLocal(int $userId, int $categoryId, array $data): int
    {
        $this->insert([
            'user_id' => $userId,
            'category_id' => $categoryId,
            'name' => $data['name'],
            'sexe' => $data['sexe'] ?? 'Mixte',
            'age_min' => (int) $data['age_min'],
            'age_max' => (int) $data['age_max'],
            'capacity' => (int) $data['capacity'],
        ]);
        return $this->lastInsertId();
    }

    public function belongsToUser(int $id, int $userId): bool
    {
        $row = $this->fetchOne(
            "SELECT id FROM {$this->table} WHERE id = :id AND user_id = :user_id",
            ['id' => $id, 'user_id' => $userId]
        );
        return (bool) $row;
    }

    public function autoAssign(): void
    {
        $participants = $this->fetchAllWhere(
            'SELECT id, sexe, age FROM participants ORDER BY name ASC, id ASC'
        );

        $dortoirs = $this->byCategory('dortoir');
        $ateliers = $this->byCategory('atelier');

        self::$connection->exec('UPDATE participants SET dortoir_id = NULL, atelier_id = NULL');

        $usedDortoirs = [];
        $usedAteliers = [];

        foreach ($participants as $participant) {
            foreach ($dortoirs as $dortoir) {
                $id = (int) $dortoir['id'];
                $used = $usedDortoirs[$id] ?? 0;
                $sexeOk = $dortoir['sexe'] === 'Mixte' || $dortoir['sexe'] === $participant['sexe'];
                if (
                    $used < (int) $dortoir['capacity']
                    && $sexeOk
                    && (int) $participant['age'] >= (int) $dortoir['age_min']
                    && (int) $participant['age'] <= (int) $dortoir['age_max']
                ) {
                    $stmt = self::$connection->prepare(
                        'UPDATE participants SET dortoir_id = :dortoir_id WHERE id = :id'
                    );
                    $stmt->execute(['dortoir_id' => $id, 'id' => $participant['id']]);
                    $usedDortoirs[$id] = $used + 1;
                    break;
                }
            }

            foreach ($ateliers as $atelier) {
                $id = (int) $atelier['id'];
                $used = $usedAteliers[$id] ?? 0;
                if (
                    $used < (int) $atelier['capacity']
                    && (int) $participant['age'] >= (int) $atelier['age_min']
                    && (int) $participant['age'] <= (int) $atelier['age_max']
                ) {
                    $stmt = self::$connection->prepare(
                        'UPDATE participants SET atelier_id = :atelier_id WHERE id = :id'
                    );
                    $stmt->execute(['atelier_id' => $id, 'id' => $participant['id']]);
                    $usedAteliers[$id] = $used + 1;
                    break;
                }
            }
        }
    }
}
