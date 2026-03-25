<?php

declare(strict_types=1);

use App\Commands\GenerateCommand;
use App\Commands\ImportCommand;
use App\Controllers\AnalyzerController;
use App\Controllers\GeneratorController;
use App\Controllers\ParserController;
use App\Core\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$router = new Router();

$router->get('/', static function (): void {
    header('Content-Type: text/html; charset=utf-8');
    echo <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>CSV Upload</title></head>
<body>
    <h1>Upload CSV</h1>
    <form action="/parse" method="post" enctype="multipart/form-data">
        <input type="file" name="csv_file" accept=".csv" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
HTML;
});

$router->post('/parse', [new ParserController(new ImportCommand()), 'parse']);
$router->get('/generate', [new GeneratorController(new GenerateCommand()), 'generate']);
$router->get('/analyze', [new AnalyzerController(), 'analyze']);

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
