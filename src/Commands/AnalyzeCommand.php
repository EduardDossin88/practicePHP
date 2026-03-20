<?php

namespace App\Commands;

use App\Services\Database;
use PDO;

class AnalyzeCommand
{
    public function execute(): void
    {
        $db = Database::getInstance()->getConnection();

        if ($db === null) {
            echo "❌ Ошибка: Нет подключения к базе данных.\n";
            return;
        }

        echo "\n📊 СТАТИСТИКА ПО БАЗЕ ДАННЫХ 📊\n";
        echo str_repeat("-", 40) . "\n";

        // Считаем сколько пользователй
        $totalQuery = $db->query("SELECT COUNT(*) FROM users");
        $totalUsers = $totalQuery !== false ? $totalQuery->fetchColumn() : 0;
        echo "Всего записей в базе: $totalUsers\n";

        // Подсчёт зп
        $salaryQuery = $db->query("SELECT AVG(salary) FROM users");
        $avgSalary = $salaryQuery !== false ? $salaryQuery->fetchColumn() : 0;

        if (is_numeric($avgSalary)) {
            echo "Средняя зарплата: " . number_format((float)$avgSalary, 2, '.', ' ') . " руб.\n";
        }

        // 3. Выводим Топ-3 города
        echo "\nТоп-3 города по населению:\n";
        $stmt = $db->query("SELECT city, COUNT(*) as count FROM users GROUP BY city ORDER BY count DESC LIMIT 3");

        if ($stmt !== false) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $city = isset($row['city']) && is_string($row['city']) ? $row['city'] : 'Неизвестно';
                $count = isset($row['count']) ? (int)$row['count'] : 0;

                echo " 📍 $city: $count чел.\n";
            }
        }

        echo str_repeat("-", 40) . "\n";
    }
}
