<?php

namespace App\Commands;

use App\Models\User;
use App\Services\Database;

class ImportCommand
{
    public function execute(): void
    {
        $filePath = __DIR__ . '/../../data/data.csv';

        if (!file_exists($filePath)) {
            throw new \Exception("Файл не найден: $filePath");
        }

        $file = fopen($filePath, 'r');
        if ($file === false) {
            throw new \Exception("Нэ удалось открыть файл");
        }

        $db = Database::getInstance()->getConnection();
        if ($db === null) {
            fclose($file);
            throw new \Exception("База данных не доступна");
        }

        $db->exec("CREATE TABLE IF NOT EXISTS users (
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

        fgetcsv($file, 0, ",", "\"", "");

        $count = 0;

        $db->beginTransaction();

        try {
            while (($row = fgetcsv($file, 0, ",", "\"", "")) !== false) {
                if (count($row) >= 9) {
                    User::insert([
                        'country' => $row[0],
                        'city' => $row[1],
                        'is_active' => $row[2],
                        'gender' => $row[3],
                        'birth_date' => $row[4],
                        'salary' => $row[5],
                        'has_children' => $row[6],
                        'family_status' => $row[7],
                        'registration_date' => $row[8]
                    ]);
                    $count++;
                } else {
                    echo "Строка пропущена: ожидалось 9 колонок, получено " . count($row) . ". Содержимое: " . implode('|', $row) . "\n";
                }
            }
            $db->commit();
            echo "Успешно импортировано $count записей со ВСЕМИ полями!\n";
        } catch (\Exception $e) {
            $db->rollBack();
            throw new \Exception("Ошибка при импорте: " . $e->getMessage());
        } finally {
            fclose($file);
        }
    }
}
