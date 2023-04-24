<?php

namespace src\app;

use SimpleXMLElement;
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

    public function __construct(array $templateValues)
    {
        $this->url = Url::getRequestUrl();
        $this->uri = Url::getRequestUri();
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->body = file_get_contents('php://input');
        $this->headers = getallheaders();
        $this->parameters = $_GET;
        $this->cookies = $_COOKIE;
        $this->templateValues = $templateValues;
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
        return json_decode($this->body, true);
    }

    public function getFormData(): ?array
    {
        if (empty($_POST)) {
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
}

?>