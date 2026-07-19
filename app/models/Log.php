<?php
namespace App\Models;

use App\Models\Model;

class Log extends Model
{
    public function __construct()
    {
        parent::__construct("logs");
    }
   
    public function create(array $datas)
    {
        return $this->insert($datas);
    }
    // Exemple : récupérer les logs par action
    public function getByLevel(string $action)
    {
        $stmt = self::$connection->prepare("SELECT * FROM {$this->table} WHERE action = :action");
        $stmt->execute(['action' => $action]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
