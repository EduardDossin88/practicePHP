<?php

declare(strict_types=1);

namespace App\Commands;

use Faker\Factory;
use RuntimeException;

/**
 * Generates synthetic CSV data.
 */
final class GenerateCommand
{
    public function execute(int $count): string
    {
        $faker = Factory::create();
        $dataPath = dirname(__DIR__, 2) . '/data/data.csv';
        $handle = fopen($dataPath, 'wb');

        if ($handle === false) {
            throw new RuntimeException('Unable to open CSV file for writing.');
        }

        fputcsv($handle, [
            'first_name',
            'last_name',
            'email',
            'birth_date',
            'is_active',
            'has_children',
            'created_at',
        ]);

        for ($index = 0; $index < $count; $index++) {
            fputcsv($handle, [
                $faker->firstName(),
                $faker->lastName(),
                $faker->unique()->safeEmail(),
                $faker->date('Y-m-d'),
                $faker->boolean() ? 1 : 0,
                $faker->boolean() ? 1 : 0,
                $faker->dateTimeBetween('-2 years')->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($handle);

        return $dataPath;
    }
}
