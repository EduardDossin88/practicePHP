<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\Database;
use PDO;
use RuntimeException;

/**
 * Imports CSV data into users table.
 */
final class ImportCommand
{
    public function execute(?string $filePath = null): void
    {
        $path = $filePath ?? dirname(__DIR__, 2) . '/data/data.csv';
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException('CSV file cannot be opened.');
        }

        $db = Database::getInstance()->getConnection();
        $this->ensureUsersTable($db);

        $headers = fgetcsv($handle);
        if (!is_array($headers)) {
            fclose($handle);
            throw new RuntimeException('CSV headers are invalid.');
        }

        $insert = $db->prepare(
            'INSERT INTO users (first_name, last_name, email, birth_date, is_active, has_children, created_at)
            VALUES (:first_name, :last_name, :email, :birth_date, :is_active, :has_children, :created_at)'
        );

        while (($row = fgetcsv($handle)) !== false) {
            $record = array_combine($headers, $row);
            if (!is_array($record)) {
                continue;
            }

            $insert->execute([
                'first_name' => (string) ($record['first_name'] ?? ''),
                'last_name' => (string) ($record['last_name'] ?? ''),
                'email' => (string) ($record['email'] ?? ''),
                'birth_date' => (string) ($record['birth_date'] ?? ''),
                'is_active' => (int) ($record['is_active'] ?? 0),
                'has_children' => (int) ($record['has_children'] ?? 0),
                'created_at' => (string) ($record['created_at'] ?? date('Y-m-d H:i:s')),
            ]);
        }

        fclose($handle);
    }

    private function ensureUsersTable(PDO $db): void
    {
        $db->exec(
            'CREATE TABLE IF NOT EXISTS users (
                id SERIAL PRIMARY KEY,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(255) NOT NULL,
                birth_date DATE NOT NULL,
                is_active SMALLINT NOT NULL,
                has_children SMALLINT NOT NULL,
                created_at TIMESTAMP NOT NULL
            )'
        );
    }
}
