<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Database;
use PDO;
use PDOStatement;

/**
 * Base model with generic query helpers.
 */
abstract class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function all(): array
    {
        $statement = $this->db->query(sprintf('SELECT * FROM %s', static::tableName()));
        $rows = $statement !== false ? $statement->fetchAll() : [];

        return is_array($rows) ? $rows : [];
    }

    /**
     * @param array<string, mixed> $filters
     * @return list<array<string, mixed>>
     */
    public function findByFilters(array $filters): array
    {
        $query = sprintf('SELECT * FROM %s', static::tableName());
        $conditions = [];
        $params = [];

        foreach ($filters as $column => $value) {
            if (is_array($value) && isset($value['from'], $value['to'])) {
                $fromParam = $column . '_from';
                $toParam = $column . '_to';
                $conditions[] = sprintf('%s >= :%s AND %s <= :%s', $column, $fromParam, $column, $toParam);
                $params[$fromParam] = $value['from'];
                $params[$toParam] = $value['to'];
                continue;
            }

            $conditions[] = sprintf('%s = :%s', $column, $column);
            $params[$column] = $value;
        }

        if ($conditions !== []) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $statement = $this->db->prepare($query);
        $this->bindParams($statement, $params);
        $statement->execute();

        $rows = $statement->fetchAll();

        return is_array($rows) ? $rows : [];
    }

    /**
     * @param array<string, mixed> $params
     */
    private function bindParams(PDOStatement $statement, array $params): void
    {
        foreach ($params as $name => $value) {
            $statement->bindValue(':' . $name, $value);
        }
    }

    abstract protected static function tableName(): string;
}
