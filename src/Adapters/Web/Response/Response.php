<?php

namespace App\Adapters\Web\Response;

class Response
{
    /**
     * @param array<string, mixed> $data
     */
    public function json(array $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');

        http_response_code($statusCode);

        echo json_encode($data) . PHP_EOL;
    }
}
