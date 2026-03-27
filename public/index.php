<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AnalyzerController;
use App\Controllers\GeneratorController;
use App\Controllers\ParserController;
use Core\Router;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$router = new Router();

$router->get('/', function () {
    echo '
    <h2>CSV Import System</h2>
    <h3>Загрузка CSV файла (До 5 МБ)</h3>
    <form action="/parse" method="POST" enctype="multipart/form-data">
        <input type="file" name="csv_file" accept=".csv" required>
        <button type="submit">Загрузить и импортировать</button>
    </form>
    ';
});

$router->get('/analyze', [new AnalyzerController(), 'analyze']);

$router->post('/parse', [new ParserController(), 'parse']);

$router->post('/generate', [new GeneratorController(), 'generate']);

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$router->resolve($method, $uri);
