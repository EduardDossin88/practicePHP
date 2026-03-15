<?php

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $host = getenv('DB_HOST')?: 'db';
        $db = getenv('POSTGRES_DB');
        $user = getenv('POSTGRES_USER');
        $pass = getenv('POSTGRES_PASSWORD');

        $dsn = "pgsql:host=$host;dbname=$db";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e)
        {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }
    public static function getInstance()
    {
    if(self::$instance == null){
        self::$instance = new self();
    }
    return self::$instance;
    }
    public function getConnection()
    {
        return $this->pdo;
    }
}
