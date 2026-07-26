<?php
namespace App\Models;

use PDO;
use PDOException;

class Model
{
    protected static ?PDO $connection = null;
    protected string $table;

    public function __construct(string $table)
    {
        $this->table = $table;
        self::connect();
    }

    protected static function connect(): void
    {
        if (self::$connection) {
            return;
        }

        try {
            self::$connection = new PDO(
                'mysql:host=' . ($_ENV['DB_HOST'] ?? 'localhost')
                . ';dbname=' . ($_ENV['DB_NAME'] ?? '')
                . ';charset=utf8mb4',
                $_ENV['DB_USER'] ?? '',
                $_ENV['DB_MDP'] ?? '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        } catch (PDOException $e) {
            die('Erreur de connexion : ' . $e->getMessage());
        }
    }

    public static function pdo(): PDO
    {
        self::connect();
        return self::$connection;
    }

    public function all(int $fetchMode = PDO::FETCH_ASSOC): array
    {
        $stmt = self::$connection->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll($fetchMode);
    }

    public function find(int $id, int $fetchMode = PDO::FETCH_ASSOC)
    {
        $stmt = self::$connection->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch($fetchMode);
    }

    public function insert(array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = self::$connection->prepare($sql);
        return $stmt->execute($data);
    }

    public function updateById(int $id, array $data): bool
    {
        if ($data === []) {
            return false;
        }

        $set = [];
        foreach (array_keys($data) as $key) {
            $set[] = "$key = :$key";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . ' WHERE id = :id';
        $stmt = self::$connection->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = self::$connection->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function lastInsertId(): int
    {
        return (int) self::$connection->lastInsertId();
    }

    public function count(string $where = '1=1', array $params = []): int
    {
        $stmt = self::$connection->prepare("SELECT COUNT(*) FROM {$this->table} WHERE $where");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function sum(string $column, string $where = '1=1', array $params = []): float
    {
        $stmt = self::$connection->prepare("SELECT COALESCE(SUM($column), 0) FROM {$this->table} WHERE $where");
        $stmt->execute($params);
        return (float) $stmt->fetchColumn();
    }

    protected function fetchAllWhere(string $sql, array $params = [], int $fetchMode = PDO::FETCH_ASSOC): array
    {
        $stmt = self::$connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll($fetchMode);
    }

    protected function fetchOne(string $sql, array $params = [], int $fetchMode = PDO::FETCH_ASSOC)
    {
        $stmt = self::$connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch($fetchMode);
    }
}
