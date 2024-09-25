<?php

namespace App\Adapters\Web\Request;

class PhpInput
{
    /**
     * @return array<string, mixed>
     */
    public function getGet(): array
    {
        return $_GET;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPost(): array
    {
        return $_POST;
    }

    /**
     * @return array<string, mixed>
     */
    public function getJson(): array
    {
        $requestBody = file_get_contents('php://input') ?: '';

        return json_decode($requestBody, true) ?: [];
    }
}