<?php

namespace App\Adapters\Web\Request;

use App\Adapters\Web\Request\Enums\RequestMethod;
use RuntimeException;

readonly class Request
{
    private PhpInput $input;

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

    public function __construct(?PhpInput $input = null)
    {
        $this->input = $input ?? new PhpInput();
        $this->params = array_merge(
            $this->input->getGet(),
            $this->input->getPost(),
            $this->input->getJson()
        );
        $this->server = $_SERVER;
        $this->method = RequestMethod::tryFrom($this->server['REQUEST_METHOD']) ?? RequestMethod::GET;
        $this->uri = filter_var(
            value: $this->server['REQUEST_URI'],
            filter: FILTER_SANITIZE_URL,
            options: FILTER_NULL_ON_FAILURE
        ) ?? '/';
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

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getSanitizedUri(): string
    {
        $sanitizedUrl = strtok($this->getUri(), '?');
        if (!$sanitizedUrl) {
            return '/';
        }

        return $sanitizedUrl;
    }
}
