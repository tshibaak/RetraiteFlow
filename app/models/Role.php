<?php
namespace App\Models;

class Role extends Model
{
    public function __construct()
    {
        parent::__construct('roles');
    }

    public function findByName(string $name)
    {
        return $this->fetchOne(
            "SELECT * FROM {$this->table} WHERE name = :name",
            ['name' => $name]
        );
    }
}
