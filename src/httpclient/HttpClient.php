<?php

namespace src\httpclient;

use src\util\Url;

class HttpClient
{
    public const GET = 'GET';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const PATCH = 'PATCH';
    public const DELETE = 'DELETE';

    protected string $url;
    protected string $method;
    protected array $parameters;
    protected array $headers;
    protected string|array $body;
    protected bool $secure;
    protected array $customOptions;

    public static function new(): static
    {
        return new static();
    }

    public function __construct()
    {
        $this->url = '';
        $this->method = $this::GET;
        $this->parameters = [];
        $this->headers = [];
        $this->body = '';
        $this->secure = true;
        $this->customOptions = [];
    }

    public function url(string $url): static
    {
        if (str_contains($url, '?')) {
            $split = explode('?', $url);

            $url = $split[0];

            $decodedParameters = Url::urldecodeParameters($split[1]);

            foreach ($decodedParameters as $key => $value) {
                $this->parameters[$key] = $value;
            }
        }

        $this->url = trim($url);

        return $this;
    }

    public function method(string $method)
    {
        $this->method = trim(
            strtolower($method)
        );

        return $this;
    }

    public function parameters(array $parameters): static
    {
        foreach ($parameters as $key => $value) {
            $this->parameters[$key] = $value;
        }

        return $this;
    }

    public function headers(array $headers): static
    {
        $this->headers = $headers;

        return $this;
    }

    public function body(string|array $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function secure(bool $secure): static
    {
        $this->secure = $secure;

        return $this;
    }

    public function timeout(int $seconds): static
    {
        $this->customOptions[CURLOPT_TIMEOUT] = $seconds;

        return $this;
    }

    public function execute(): HttpResponse
    {
        return HttpRequest::custom(
            $this->url,
            $this->method,
            $this->parameters,
            $this->headers,
            $this->body,
            $this->secure,
            $this->customOptions
        );
    }
}

?>