<?php

namespace App\Services;

use PDO;

class Database
{
    private static ?self $instance = null;
    private ?PDO $pdo = null;

    private function __construct()
    {
        $host = (string)($_ENV['DB_HOST'] ?: 'db');
        $db = (string)($_ENV['POSTGRES_DB'] ?? '');
        $user = (string)($_ENV['POSTGRES_USER'] ?? '');
        $pass = (string)($_ENV['POSTGRES_PASSWORD'] ?? '');

        $dsn = "pgsql:host=$host;dbname=$db";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \Exception("Ошибка подключения к базе: " . $e->getMessage());
        }
    }
    public static function getInstance(): self
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function getConnection(): ?PDO
    {
        return $this->pdo;
    }
}
