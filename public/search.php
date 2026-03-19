<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\Database;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json; charset=utf-8');

try {
    $dbInstance = Database::getInstance();
    $pdo = $dbInstance->getConnection();

    if ($pdo === null) {
        http_response_code(500);
        echo json_encode(['error' => 'Не удалось установить соединение с БД']);
        exit;
    }

    $query = "SELECT * FROM users WHERE 1=1";
    $params = [];


    if (!empty($_GET['country'])) {
        $query .= " AND country = :country";
        $params[':country'] = $_GET['country'];
    }

    if (isset($_GET['is_active']) && $_GET['is_active'] !== '') {
        $query .= " AND is_active = :is_active";
        $params[':is_active'] = filter_var($_GET['is_active'], FILTER_VALIDATE_BOOLEAN);
    }


    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => "Ошибка базы данных: " . $e->getMessage()]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => "Общая ошибка: " . $e->getMessage()]);
}
