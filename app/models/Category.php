<?php
namespace App\Models;

class Category extends Model
{
    public function __construct()
    {
        parent::__construct('categories');
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
}
