<?php

namespace App\Controllers;

use App\Models\User;

class AnalyzerController
{
    public function analyze(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $filters = [];

        $allowedExactFields = ['id', 'country', 'city', 'is_active', 'gender', 'salary', 'has_children', 'family_status'];

        foreach ($allowedExactFields as $field) {
            if (isset($_GET[$field]) && $_GET[$field] !== '') {
                $filters[$field] = $_GET[$field];
            }
        }
        $dateFields = ['birth_date', 'registration_date'];

        foreach ($dateFields as $dateField) {
            $from = $_GET[$dateField . '_from'] ?? null;
            $to = $_GET[$dateField . '_to'] ?? null;

            if (($from !== null && $from !== '') || ($to !== null && $to !== '')) {
                $filters[$dateField] = [];
                if ($from !== null && $from !== '') {
                    $filters[$dateField]['from'] = $from;
                }
                if ($to !== null && $to !== '') {
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
