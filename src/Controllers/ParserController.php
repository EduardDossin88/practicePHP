<?php

namespace App\Controllers;

use App\Commands\ImportCommand;

class ParserController
{
    public function parse(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $file = $_FILES['csv_file'] ?? null;

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Файл не был загружен или размер превышен'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Файл слишком большой. Максимальный размер: 5 МБ'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (strtolower($extension) !== 'csv') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Разрешены только файлы формата CSV'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $targetPath = __DIR__ . '/../../data/data.csv';

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Не удалось сохранить файл на сервере'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $fileContent = file($targetPath);

        if (!is_array($fileContent) || count($fileContent) <= 1) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => "Файл загружен, но он пустой или нечитаем."
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        try {

            $importCommand = new ImportCommand();

            ob_start();
            $importCommand->execute();
            $output = ob_get_clean();

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Файл успешно загружен и обработан базой данных!',
                'import_log' => trim($output ?: '')
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            if (ob_get_length() > 0) {
                ob_end_clean();
            }
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Не удалось сохранить файл на сервере: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}
