<?php

namespace App\Commands;

use App\Services\Database;
use PDO;
class ImportCommand
{
    public function execute(): void
    {
        $filePath = __DIR__ . '/../../data/data.csv';

        if (!file_exists($filePath)) {
            echo "❌ Файл не найден: $filePath\n";
            return;
        }

        $file = fopen($filePath, 'r');
        if ($file === false) {
            echo "❌ Не удалось открыть файл.\n";
            return;
        }

        $db = Database::getInstance()->getConnection();
        if ($db === null) {
            fclose($file);
            echo "❌ База данных недоступна.\n";
            return;
        }

        fgetcsv($file, 0, ",", "\"", "");

        $sql = "INSERT INTO users (country, city, salary) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);

        $count = 0;
        $db->beginTransaction();

        try {
            while (($row = fgetcsv($file, 0, ",", "\"", "")) !== false) {
                $stmt->execute([$row[0], $row[1], $row[5]]);
                $count++;
            }
            $db->commit();
            echo "✅ Успешно импортировано $count записей!\n";
        } catch (\Exception $e) {
            $db->rollBack();
            echo "❌ Ошибка при импорте: " . $e->getMessage() . "\n";
        }

        fclose($file);
    }
}
