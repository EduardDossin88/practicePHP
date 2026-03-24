<?php

namespace App\Commands;

use Faker\Factory;

class GenerateCommand
{
    /**
     * @param array<int, string> $args
     */
    public function execute(array $args): void
    {
        $quantity = isset($args[2]) ? (int) $args[2] : 10;

        if ($quantity <= 0) {
            echo "❌ Ошибка: количество должно быть больше нуля.\n";
            return;
        }

        $faker = Factory::create();
        $filePath = __DIR__ . '/../../data/data.csv';
        $file = fopen($filePath, "w");

        if ($file === false) {
            echo "❌ Ошибка: Не удалось открыть файл для записи.\n";
            return;
        }

        fputcsv($file, ['country', 'city', 'is_active', 'gender', 'birth_Date', 'salary', 'has_children', 'family_status', 'registration_date'], ",", "\"", "");

        for ($i = 0; $i < $quantity; $i++) {
            fputcsv($file, [
                $faker->country,
                $faker->city,
                $faker->boolean ? 1 : 0,
                $faker->randomElement(['male', 'female']),
                $faker->date('Y-m-d', '2005-01-01'),
                $faker->numberBetween(30000, 150000),
                $faker->boolean ? 1 : 0,
                $faker->randomElement(['single', 'married', 'divorced']),
                $faker->date('Y-m-d')
            ], ",", "\"", "");
        }

        fclose($file);
        echo "✅ Успешно сгенерировано $quantity строк!\n";
    }
}
