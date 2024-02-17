<?php

namespace src\app;

use JsonException;
use SimpleXMLElement;
use src\requests\Request as CustomRequest;
use src\router\Route;
use src\util\FormData;
use src\util\Url;

class Request
{
    protected string $url;
    protected string $uri;
    protected string $path;
    protected ?string $queryString;
    protected string $method;
    protected string $body;
    protected array $headers;
    protected array $parameters;
    protected array $cookies;
    protected array $vars;
    protected ?CustomRequest $request = null;

    public function __construct(?Route $route = null)
    {
        $url = Url::getRequestUrl();
        $uri = ($_ENV['SERVER_IS_NESTED']) ? Url::getRequestUri() : Url::getPath();
        $path = ($route) ? $route->getPath() : '';
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
                $lcKey = strtolower($key);
                $headers[$lcKey] = $val;
            }
        );
        $vars = ($route) ? $route->getVars() : [];
        $request = ($route) ? $route->getRequest() : null;

        $this->url = $url;
        $this->uri = $uri;
        $this->path = $path;
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

    public function getPath(): string
    {
        return $this->path;
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
        $lcKey = strtolower($key);

        if (!key_exists($lcKey, $this->headers)) {
            return null;
        }

        return $this->headers[$lcKey];
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
        if (empty($this->body)) {
            return null;
        }

        $assoc = json_decode($this->body, true);

        if (is_null($assoc) && json_last_error_msg() != 'No error') {
            throw new JsonException(json_last_error_msg());
        }

        return $assoc;
    }

    public function getFormData($unique = true): ?array
    {
        if (empty($this->body)) {
            return null;
        }

        return FormData::decode($this->body, $unique);
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

    public function getRequest(): ?CustomRequest
    {
        return $this->request;
    }
}
