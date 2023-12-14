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
    protected ?string $queryString;
    protected string $method;
    protected string $body;
    protected array $headers;
    protected array $parameters;
    protected array $cookies;
    protected array $vars;
    protected ?RequestObject $request = null;

    public function __construct(array $vars = [], ?string $request = null)
    {
        $url = Url::getRequestUrl();
        $uri = Url::getRequestUri();
        $queryString = Url::getQueryString();
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        $body = file_get_contents('php://input');
        $parameters = Url::urlDecodeParameters($queryString);
        $cookies = $_COOKIE;
        $rawHeaders = getallheaders();
        $headers = [];
        array_walk(
            $rawHeaders,
            function (string $val, string $key) use (&$headers) {
                $lowerCaseKey = strtolower($key);
                $headers[$lowerCaseKey] = $val;
            }
        );

        $this->url = $url;
        $this->uri = $uri;
        $this->queryString = $queryString;
        $this->method = $method;
        $this->body = $body;
        $this->headers = $headers;
        $this->parameters = $parameters;
        $this->cookies = $cookies;
        $this->vars = $vars;

        if ($request) {
            $this->request = new $request($this);
        }
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getQueryString(): ?string
    {
        return $this->queryString;
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

    public function getParameter(string $key): null|string|array
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

    public function getVars(): array
    {
        return $this->vars;
    }

    public function getVar(string $key): ?string
    {
        if (!key_exists($key, $this->vars)) {
            return null;
        }

        return $this->vars[$key];
    }

    public function getRequest(): ?RequestObject
    {
        return $this->request;
    }
}
