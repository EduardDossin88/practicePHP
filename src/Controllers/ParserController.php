<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Commands\ImportCommand;
use RuntimeException;
use Throwable;

/**
 * Handles CSV upload and parsing endpoint.
 */
final class ParserController
{
    public function __construct(private readonly ImportCommand $importCommand)
    {
    }

    public function parse(): void
    {
        header('Content-Type: application/json');

        try {
            if (!isset($_FILES['csv_file']) || !is_array($_FILES['csv_file'])) {
                throw new RuntimeException('CSV file is required.');
            }

            $file = $_FILES['csv_file'];
            $name = (string) ($file['name'] ?? '');
            $size = (int) ($file['size'] ?? 0);
            $tmpName = (string) ($file['tmp_name'] ?? '');

            if (strtolower(pathinfo($name, PATHINFO_EXTENSION)) !== 'csv') {
                throw new RuntimeException('Only .csv files are allowed.');
            }

            if ($size > 5 * 1024 * 1024) {
                throw new RuntimeException('File is too large. Maximum size is 5MB.');
            }

            $destination = dirname(__DIR__, 2) . '/data/data.csv';
            if (!move_uploaded_file($tmpName, $destination)) {
                throw new RuntimeException('Failed to save uploaded file.');
            }

            $this->importCommand->execute($destination);

            http_response_code(200);
            echo json_encode(['success' => true], JSON_THROW_ON_ERROR);
        } catch (Throwable $throwable) {
            http_response_code(500);
            echo json_encode(['error' => $throwable->getMessage()], JSON_THROW_ON_ERROR);
        }
    }
}
