<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\Database;

header('Content-Type: application/json; charset=utf-8');

$db = Database::getInstance()->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка подключения к базе данных']);
    exit;
}

$sql = "SELECT * FROM users WHERE 1=1";
$params = [];

$allowedFilters = [
    'country', 'city', 'is_active', 'gender',
    'birth_date', 'has_children', 'family_status', 'registration_date'
];

foreach ($allowedFilters as $filter) {
    if (isset($_GET[$filter]) && $_GET[$filter] !== '') {
        $sql .= " AND $filter = :$filter";
        $params[$filter] = $_GET[$filter];
    }
}

if (!empty($_GET['min_salary'])) {
    $sql .= " AND salary >= :min_salary";
    $params['min_salary'] = (int)$_GET['min_salary'];
}
if (!empty($_GET['max_salary'])) {
    $sql .= " AND salary <= :max_salary";
    $params['max_salary'] = (int)$_GET['max_salary'];
}

try {
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'total_found' => count($results),
        'filters_applied' => $params,
        'data' => $results
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
