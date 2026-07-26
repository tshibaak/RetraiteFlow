<?php
namespace App\Models;

class Groupe extends Model
{
    public function __construct()
    {
        parent::__construct('groupes');
    }

    public function findByName(string $name)
    {
        return $this->fetchOne(
            "SELECT * FROM {$this->table} WHERE name = :name",
            ['name' => $name]
        );
    }

    public function idByName(string $name): ?int
    {
        $row = $this->findByName($name);
        return $row ? (int) $row['id'] : null;
    }

    public function resolveId(string $label): ?int
    {
        $value = mb_strtoupper(trim($label), 'UTF-8');
        $map = [
            'SOLVABLE' => 'solvable',
            'ACCRÉDITÉ' => 'accredited',
            'ACCREDITE' => 'accredited',
            'ACCREDITED' => 'accredited',
            'CAS SOCIAL' => 'social_case',
            'CAS_SOCIAL' => 'social_case',
            'SOCIAL_CASE' => 'social_case',
        ];

        $name = $map[$value] ?? mb_strtolower(trim($label), 'UTF-8');
        return $this->idByName($name);
    }

    public function label(string $name): string
    {
        return match ($name) {
            'solvable' => 'Solvable',
            'accredited' => 'Accrédité',
            'social_case' => 'Cas Social',
            default => $name,
        };
    }
}
