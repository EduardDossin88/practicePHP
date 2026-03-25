<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use Throwable;

/**
 * Handles filtered analysis endpoint.
 */
final class AnalyzerController
{
    public function analyze(): void
    {
        header('Content-Type: application/json');

        try {
            $userModel = new User();
            $filters = $this->buildFilters($_GET);
            $result = $filters === [] ? $userModel->all() : $userModel->findByFilters($filters);

            echo json_encode($result, JSON_THROW_ON_ERROR);
        } catch (Throwable $throwable) {
            http_response_code(500);
            echo json_encode(['error' => $throwable->getMessage()], JSON_THROW_ON_ERROR);
        }
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    private function buildFilters(array $input): array
    {
        $filters = [];

        foreach ($input as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            if ($key === 'birth_date_from' || $key === 'birth_date_to') {
                $filters['birth_date'] ??= ['from' => '', 'to' => ''];
                if ($key === 'birth_date_from') {
                    $filters['birth_date']['from'] = (string) $value;
                }
                if ($key === 'birth_date_to') {
                    $filters['birth_date']['to'] = (string) $value;
                }
                continue;
            }

            if (is_scalar($value) && $value !== '') {
                $filters[$key] = $value;
            }
        }

        if (isset($filters['birth_date'])) {
            $birthDate = $filters['birth_date'];
            if (!is_array($birthDate) || $birthDate['from'] === '' || $birthDate['to'] === '') {
                unset($filters['birth_date']);
            }
        }

        return $filters;
    }
}
