<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\SearchController;
use App\Controllers\UploadController;
use Core\Router; // <-- Добавили

$router = new Router();

$router->get('/', function () {
    echo '
    <h2>Загрузка CSV файла (До 5 МБ)</h2>
    <form action="/api/upload" method="POST" enctype="multipart/form-data">
        <input type="file" name="csv_file" accept=".csv" required>
        <button type="submit">Загрузить и импортировать</button>
    </form>
    ';
});

// Направляем поиск в Контроллер:
$router->get('/api/search', [new SearchController(), 'search']);

$router->post('/api/upload', [new UploadController(), 'upload']);

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$router->resolve($method, $uri);
