<?php

namespace src\app;

use JsonException;
use SimpleXMLElement;
use src\requests\Request as RequestObject;
use src\util\Url;

class Request
{
    protected string $url;
    protected string $uri;
    protected string $method;
    protected string $body;
    protected array $headers;
    protected array $parameters;
    protected array $cookies;
    protected array $templateValues;
    protected ?string $payload = null;

    public function __construct(array $templateValues, ?string $payload)
    {
        $this->url = Url::getRequestUrl();
        $this->uri = Url::getRequestUri();
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        $this->body = file_get_contents('php://input');

        $rawHeaders = getallheaders();
        array_walk(
            $rawHeaders,
            function (string $val, string $key) use (&$headers) {
                $lowerCaseKey = strtolower($key);
                $headers[$lowerCaseKey] = $val;
            }
        );
        $this->headers = $headers;

        $this->parameters = $_GET;
        $this->cookies = $_COOKIE;
        $this->templateValues = $templateValues;
        $this->payload = $payload;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $key): ?string
    {
        if (!key_exists($key, $this->headers)) {
            return null;
        }

        return $this->headers[$key];
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getParameter(string $key): ?string
    {
        if (!key_exists($key, $this->parameters)) {
            return null;
        }

        return $this->parameters[$key];
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }

    public function getCookie(string $key): ?string
    {
        if (!key_exists($key, $this->cookies)) {
            return null;
        }

        return $this->cookies[$key];
    }

    public function getJson(): ?array
    {
        $assoc = json_decode($this->body, true);

        if (is_null($assoc) && json_last_error_msg() != 'No error') {
            throw new JsonException(json_last_error_msg());
        }

        return $assoc;
    }

    public function getFormData(): ?array
    {
        if (!$_POST) {
            return null;
        }

        return $_POST;
    }

    public function getXml(): ?SimpleXMLElement
    {
        return simplexml_load_string($this->body);
    }

    public function getTemplateValues(): array
    {
        return $this->templateValues;
    }

    public function getTemplateValue(string $key): ?string
    {
        if (!key_exists($key, $this->templateValues)) {
            return null;
        }

        return $this->templateValues[$key];
    }

    public function getPayload(): ?RequestObject
    {
        if (!$this->payload) {
            return null;
        }

        return new $this->payload($this->getJson());
    }
}
