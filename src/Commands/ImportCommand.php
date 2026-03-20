<?php

namespace App\Commands;

use App\Services\Database;

class ImportCommand
{
    public function execute(): void
    {
        $filePath = __DIR__ . '/../../data/data.csv';

        if (!file_exists($filePath)) {
            echo "❌ Файл не найден бля: $filePath\n";
            return;
        }

        $file = fopen($filePath, 'r');
        if ($file === false) {
            echo "❌ Не удалось открыть файл бля.\n";
            return;
        }

        $db = Database::getInstance()->getConnection();
        if ($db === null) {
            fclose($file);
            echo "❌ База данных недоступна бля.\n";
            return;
        }

        $db->exec("DROP TABLE IF EXISTS users");
        $db->exec("CREATE TABLE users (
            id SERIAL PRIMARY KEY,
            country VARCHAR(100),
            city VARCHAR(100),
            is_active VARCHAR(10),
            gender VARCHAR(20),
            birth_date DATE,
            salary INT,
            has_children VARCHAR(10),
            family_status VARCHAR(50),
            registration_date DATE
        )");

        // Пропуск заголовока
        fgetcsv($file, 0, ",", "\"", "");

        $sql = "INSERT INTO users (country, city, is_active, gender, birth_date, salary, has_children, family_status, registration_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);

        $count = 0;
        $db->beginTransaction();

        try {
            while (($row = fgetcsv($file, 0, ",", "\"", "")) !== false) {
                // смотрим сколько колонок и делаем отладку
                if (count($row) >= 9) {
                    $stmt->execute([
                        $row[0], $row[1], $row[2], $row[3], $row[4],
                        (int)$row[5], $row[6], $row[7], $row[8]
                    ]);
                    $count++;
                } else {
                    echo "⚠️ Долбоёь,строка пропущена: ожидалось 9 колонок, получено " . count($row) . ". Содержимое: " . implode('|', $row) . "\n";
                }
            }
            $db->commit();
            echo "✅ Успешно импортировано $count записей со ВСЕМИ полями!\n";
        } catch (\Exception $e) {
            $db->rollBack();
            echo "❌ Хуй там, ошибка при импорте: " . $e->getMessage() . "\n";
        }

        fclose($file);
    }
}
