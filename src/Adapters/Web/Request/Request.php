<?php

namespace App\Adapters\Web\Request;

use App\Adapters\Web\Request\Enums\RequestMethod;
use RuntimeException;

readonly class Request
{
    /**
     * @var array<string, mixed> $params
     */
    private array $params;

    /**
     * @var array<string, mixed> $server
     */
    private array $server;

    private RequestMethod $method;

    private string $uri;

    public function __construct()
    {
        $this->setParams();
        $this->server = $_SERVER;
        $this->method = RequestMethod::from($this->server['REQUEST_METHOD']);
        $this->uri = filter_var(
            value: $this->server['REQUEST_URI'],
            filter: FILTER_SANITIZE_URL,
            options: FILTER_NULL_ON_FAILURE
        ) ?? '';
    }

    private function setParams(): void
    {
        $params = array_merge($_GET, $_POST);

        $requestBody = file_get_contents('php://input') ?: '';

        /**
         * @var null|array<string, mixed> $requestJson
         */
        $requestJson = json_decode($requestBody, true) ?: [];
        if (is_null($requestJson)) {
            throw new RuntimeException('Invalid JSON');
        }

        $this->params = array_merge($params, $requestJson);
    }

    /**
     * @return array<string, mixed>
     */
    public function getServer(): array
    {
        return $this->server;
    }

    public function getParam(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->params;
        }

        return $this->params[$key] ?? $default;
    }

    public function getMethod(): RequestMethod
    {
        return $this->method;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function sanitizedUri(): string
    {
        $uri = $this->uri();

        if (str_contains($uri, '?')) {
            $position = strpos($uri, '?') ?: 0;

            return substr($uri, 0, $position);
        }

        return $uri;
    }
}
