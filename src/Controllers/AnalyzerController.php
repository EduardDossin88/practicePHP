<?php

namespace App\Controllers;

use App\Models\User;

class AnalyzerController
{
    public function analyze(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $filters = [];

        $allowedExactFields = ['country', 'gender', 'city', 'is_active'];

        foreach ($allowedExactFields as $field) {
            if (isset($_GET[$field]) && $_GET[$field] !== '') {
                $filters[$field] = $_GET[$field];
            }
        }
        $dateFields = ['birth_date', 'registration_date'];

        foreach ($dateFields as $dateField) {
            $from = $_GET[$dateField . '_from'] ?? null;
            $to = $_GET[$dateField . '_to'] ?? null;

            if ($from !== null && $to !== null) {
                $filters[$dateField] = [];
                if ($from !== '') {
                    $filters[$dateField]['from'] = $from;
                }
                if ($to !== '') {
                    $filters[$dateField]['to'] = $to;
                }
            }
        }
        try {
            $users = User::findByFilters($filters);

            echo json_encode([
                'success' => true,
                'total_found' => count($users),
                'data' => $users
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}
