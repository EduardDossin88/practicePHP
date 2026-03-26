<?php

namespace App\Controllers;

use App\Commands\GenerateCommand;

class GeneratorController
{
    public function generate(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'error' => 'Должен быть post запрос'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $input = json_decode((string)file_get_contents('php://input'), true);
        $quantity = $input['quantity'] ?? $_POST['quantity'] ?? 100;
        $quantity = (int)$quantity;


        if ($quantity <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'количество не должно быть больше 0'], JSON_UNESCAPED_UNICODE);
            return;
        }
        if ($quantity > 100000) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Лимит превышен. максимальное количество: 100000'], JSON_UNESCAPED_UNICODE);
            return;
        }
        try {
            $command = new GenerateCommand();
            ob_start();
            $command->execute([2 => (string)$quantity]);
            $output = ob_get_clean();

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => "Генерация завершена. Строк: $quantity",
                'log' => trim($output ?: 'данные созданы')
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Критическая ошибка при генерации: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}
