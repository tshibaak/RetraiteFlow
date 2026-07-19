<?php
namespace App\Models;

use PDO;
use PDOException;

class Model {
    protected static $connection;
    protected string  $table;

    public function __construct(string $table) {
        $this->table = $table;
        self::connect();
    }
  
    protected static function connect() {
        if (!self::$connection) {
            try {
                self::$connection = new PDO("mysql:host=".$_ENV['DB_HOST'].";dbname=".$_ENV['DB_NAME'].";charset=utf8",
                 $_ENV['DB_USER'],
                 $_ENV['DB_MDP']
                ,[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            } catch (PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }
    }
 
    public function all(int $fetchMode = PDO::FETCH_ASSOC ) {
        $stmt = self::$connection->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll($fetchMode);
    }

    public function find(int $id,int $fetchMode = PDO::FETCH_ASSOC ) {
        $stmt = self::$connection->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch($fetchMode);
    }

    public function insert(array $data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = self::$connection->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete(int $id) {
        $stmt = self::$connection->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
