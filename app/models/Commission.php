<?php
namespace App\Models;

class Commission extends Model
{
    public function __construct()
    {
        parent::__construct('commissions');
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

    public function resolveId(?string $name): ?int
    {
        if ($name === null || trim($name) === '') {
            return null;
        }

        $map = [
            'rien' => 'sans commission',
            'sans commission' => 'sans commission',
            'discipline' => 'discipline',
            'finance' => 'finance',
            'logistique' => 'logistique',
            'nettoyage' => 'nettoyage',
            'restauration' => 'restauration',
            'santé' => 'santé',
            'sante' => 'santé',
        ];

        $key = mb_strtolower(trim($name), 'UTF-8');
        $normalized = $map[$key] ?? $key;

        if ($normalized === 'sans commission') {
            return null;
        }

        $id = $this->idByName($normalized);
        if ($id !== null) {
            return $id;
        }

        $this->insert(['name' => $normalized]);
        return $this->lastInsertId();
    }
}
