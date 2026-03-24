<?php

namespace App\Models;

use App\Services\Database;
use PDO;

abstract class BaseModel
{
    protected static ?PDO $db = null;

    protected static function getDb(): PDO
    {
        if (self::$db === null) {
            self::$db = Database::getInstance()->getConnection();
            if (self::$db === null) {
                throw new \Exception("Не удалось подключиться к базе данных");
            }
        }
        return self::$db;
    }

    /**
     * @return array<int, static>
     */
    public static function all(): array
    {
        $table = static::getTableName();
        $stmt = self::getDb()->query("SELECT * FROM $table");
        if ($stmt === false) {
            return [];
        }

        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = static::create($row);
        }
        return $results;
    }

    abstract protected static function getTableName(): string;

    /**
     * @param array<string, mixed> $data
     */
    abstract protected static function create(array $data): static;

    /**
     * @param array<string, mixed> $filters
     * @return array<int, static>
     */
    public static function findByFilters(array $filters): array
    {
        $table = static::getTableName();
        $sql = "SELECT * FROM $table WHERE 1=1";
        $params = [];

        foreach ($filters as $column => $value) {
            if (is_array($value)) {
                if (isset($value['from'])) {
                    $paramFrom = $column . '_from';
                    $sql .= " AND $column >= :$paramFrom";
                    $params[$paramFrom] = $value['from'];
                }
                if (isset($value['to'])) {
                    $paramTo = $column . '_to';
                    $sql .= " AND $column <= :$paramTo";
                    $params[$paramTo] = $value['to'];
                }
            } else {
                $sql .= " AND $column = :$column";
                $params[$column] = $value;
            }
        }

        $stmt = self::getDb()->prepare($sql);
        $stmt->execute($params);

        $results = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $results[] = static::create($row);
        }
        return $results;
    }
}
