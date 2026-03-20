<?php

namespace App\Controllers;

use App\Commands\ImportCommand;

class UploadController
{
    public function upload(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'Файл не был загружен или произошла ошибка']);
            return;
        }

        $file = $_FILES['csv_file'];

        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            http_response_code(400);
            echo json_encode(['error' => 'Файл слишком большой. Максимальный размер: 5 МБ']);
            return;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (strtolower($extension) !== 'csv') {
            http_response_code(400);
            echo json_encode(['error' => 'Разрешены только файлы формата CSV']);
            return;
        }

        $targetPath = __DIR__ . '/../../data/data.csv';

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {

            // Отладка
            $fileContent = file($targetPath);

            if (!is_array($fileContent) || count($fileContent) <= 1) {
                echo json_encode([
                    'error' => "Файл загружен, но он пустой или нечитаем.",
                    'debug_info' => 'Убедитесь, что в файле есть данные после заголовка.',
                    'file_preview' => $fileContent ?: []
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                return;
            }

            $importCommand = new ImportCommand();

            ob_start();
            $importCommand->execute();
            $output = ob_get_clean();

            echo json_encode([
                'success' => true,
                'message' => 'Красава, файл успешно загружен и обработан базой данных!',
                'import_log' => trim($output ?: '')
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Не удалось сохранить файл на сервере']);
        }
    }
}
