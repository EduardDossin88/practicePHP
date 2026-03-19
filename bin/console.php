<?php

/** @var array<int, string> $argv */

require_once __DIR__ . '/../vendor/autoload.php';

$commandName = $argv[1] ?? null;

switch ($commandName) {
    case 'generate':
        $command = new \App\Commands\GenerateCommand();
        $command->execute($argv);
        break;

    case 'import':
        $command = new \App\Commands\ImportCommand();
        $command->execute();
        break;

    case 'analyze':
        $command = new \App\Commands\AnalyzeCommand();
        $command->execute();
        break;

    default:
        echo "❌ Неизвестная команда или команда не указана.\n";
        echo "Доступные команды:\n";
        echo "  php bin/console.php generate [кол-во] - Сгенерировать CSV файл\n";
        echo "  php bin/console.php import            - Импортировать CSV в БД\n";
        echo "  php bin/console.php analyze           - Показать статистику БД\n";
        break;
}