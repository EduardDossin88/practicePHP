<?php

declare(strict_types=1);

use App\Commands\GenerateCommand;
use App\Commands\ImportCommand;

require_once __DIR__ . '/../vendor/autoload.php';

$command = $argv[1] ?? '';

try {
    switch ($command) {
        case 'generate':
            $count = isset($argv[2]) ? max(1, (int) $argv[2]) : 100;
            $path = (new GenerateCommand())->execute($count);
            echo "Generated: {$path}" . PHP_EOL;
            break;

        case 'import':
            (new ImportCommand())->execute();
            echo "Import complete." . PHP_EOL;
            break;

        default:
            echo "Usage:" . PHP_EOL;
            echo "  php bin/console.php generate [count]" . PHP_EOL;
            echo "  php bin/console.php import" . PHP_EOL;
            exit(1);
    }
} catch (Throwable $throwable) {
    fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
    exit(1);
}
