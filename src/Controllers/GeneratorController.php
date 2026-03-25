<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Commands\GenerateCommand;
use Throwable;

/**
 * Handles CSV generation endpoint.
 */
final class GeneratorController
{
    public function __construct(private readonly GenerateCommand $generateCommand)
    {
    }

    public function generate(): void
    {
        header('Content-Type: application/json');

        try {
            $count = isset($_GET['count']) ? max(1, (int) $_GET['count']) : 100;
            $filePath = $this->generateCommand->execute($count);

            http_response_code(200);
            echo json_encode(['success' => true, 'file' => $filePath], JSON_THROW_ON_ERROR);
        } catch (Throwable $throwable) {
            http_response_code(500);
            echo json_encode(['error' => $throwable->getMessage()], JSON_THROW_ON_ERROR);
        }
    }
}
