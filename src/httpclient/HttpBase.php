<?php

namespace src\httpclient;

use src\exceptions\UrlException;
use src\util\MimeTypes;

abstract class HttpBase
{
    protected ?string $baseUrl;
    protected ?string $path;
    protected ?string $url;
    protected ?string $method;
    protected ?array $parameters;
    protected ?array $headers;
    protected ?array $cookies;
    protected null|string|array $body;
    protected ?bool $secure;
    protected ?int $retryCount;
    protected ?array $customOptions;

    public function __construct()
    {
        $this->baseUrl = null;
        $this->path = null;
        $this->url = null;
        $this->method = null;
        $this->parameters = [];
        $this->headers = [];
        $this->cookies = [];
        $this->body = null;
        $this->secure = null;
        $this->retryCount = null;
        $this->customOptions = null;
    }

    public function baseUrl(string $baserUrl): static
    {
        if (!parse_url($baserUrl)) {
            throw new UrlException(sprintf('Url: "%s" is invalid', $baserUrl));
        }

        $this->baseUrl = trim($baserUrl);

        return $this;
    }

    public function path(string $path): static
    {
        if (preg_match('/(.*)(#.*)$/', $path, $matches)) {
            $path = $matches[1];
        }

        if (str_contains($path, '?')) {
            $split = explode('?', $path);

            $path = $split[0];

            $parameters = $this->unSerializeParameters($split[1]);

            if ($parameters) {
                foreach ($parameters as $parameter) {
                    $this->parameters[] = $parameter;
                }
            }
        }

        $this->path = trim($path);

        return $this;
    }

    public function url(string $url): static
    {
        if (!parse_url($url)) {
            throw new UrlException(sprintf('Url: "%s" is invalid', $url));
        }

        if (preg_match('/(.*)(#.*)$/', $url, $matches)) {
            $url = $matches[1];
        }

        if (str_contains($url, '?')) {
            $split = explode('?', $url);

            $url = $split[0];

            $parameters = $this->unSerializeParameters($split[1]);

            if ($parameters) {
                foreach ($parameters as $parameter) {
                    $this->parameters[] = $parameter;
                }
            }
        }

        $this->url = trim($url);

        return $this;
    }

    public function method(string $method)
    {
        $this->method = trim(
            strtoupper($method)
        );

        return $this;
    }

    public function parameter(string $key, string|array $value, bool $replace = true): static
    {
        $lcKey = strtolower($key);

        if (!key_exists($key, $this->parameters)) {
            $this->parameters[$lcKey] = [];
        }

        if ($replace) {
            $this->parameters[$lcKey] = [];
        }

        if (is_array($value)) {
            foreach ($value as $v) {
                $this->parameter($key, $v);
            }
            return $this;
        }

        $this->parameters[$lcKey][] = (string) $value;

        return $this;
    }

    public function parameters(array $parameters, bool $replace = true): static
    {
        foreach ($parameters as $key => $value) {
            $this->parameter($key, $value, $replace);
        }

        return $this;
    }

    public function header(string $key, string|array $value, bool $replace = true): static
    {
        $lcKey = strtolower($key);

        if (!key_exists($key, $this->headers)) {
            $this->headers[$lcKey] = [];
        }

        if ($replace) {
            $this->headers[$lcKey] = [];
        }

        if (is_array($value)) {
            foreach ($value as $v) {
                $this->header($key, $v);
            }
            return $this;
        }

        $this->headers[$lcKey][] = (string) $value;

        return $this;
    }

    public function headers(array $headers, bool $replace = true): static
    {
        foreach ($headers as $key => $value) {
            $this->header($key, $value, $replace);
        }

        return $this;
    }

    public function cookie(string $key, string $value): static
    {
        $lcKey = strtolower($key);

        $this->headers[$lcKey] = (string) $value;

        return $this;
    }

    public function cookies(array $cookies, bool $replace = true): static
    {
        foreach ($cookies as $key => $value) {
            $this->cookie($key, (string) $value);
        }

        return $this;
    }

    public function setCookieStrings(string $cookieString, bool $replace = true): static
    {
        $this->header('cookie', $cookieString);

        return $this;
    }

    public function body(string $body, string $contentType = null): static
    {
        if ($contentType) {
            $this->header('content-type', $contentType);
        }

        $this->body = $body;

        return $this;
    }

    public function json(array|object $serializable): static
    {
        $this->header('content-type', MimeTypes::get('json'));

        $this->body = json_encode($serializable);

        return $this;
    }

    public function formData(array $keyValuePairs): static
    {
        $this->body = $keyValuePairs;

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

    public function timeoutMs(int $milliseconds): static
    {
        $this->customOptions[CURLOPT_TIMEOUT_MS] = $milliseconds;

        return $this;
    }

    public function retryCount(int $times): static
    {
        $this->retryCount = abs($times);

        return $this;
    }

    public function enableAutoEncoding(): static
    {
        $this->customOptions[CURLOPT_ENCODING] = '';

        return $this;
    }

    public function customOption(mixed $option, mixed $value): static
    {
        $this->customOptions[$option] = $value;

        return $this;
    }

    public function customOptions(array $options): static
    {
        foreach ($options as $key => $value) {
            $this->customOptions[$key] = $value;
        }

        return $this;
    }

    protected function unSerializeParameters(string $queryString): ?array
    {
        if (empty($queryString)) {
            return null;
        }

        $parameters = [];

        $parts = explode('&', $queryString);

        foreach ($parts as $part) {
            $partSplit = explode('=', $part, 2);
            $key = urldecode($partSplit[0] ?? '');
            $value = urldecode($partSplit[1] ?? '');

            if (!key_exists($key, $parameters)) {
                $parameters[$key] = [];
            }

            $parameters[$key][] = $value;
        }

        return $parameters;
    }

    protected function parameterExists(string $key, ?array $parameters = null): bool|int
    {
        $parameters = ($parameters)
            ? $parameters
            : $this->parameters;

        foreach ($parameters as $index => $parameter) {
            if ($parameter->getKey() == $key) {
                return $index;
            }
        }

        return false;
    }

    protected function headerExists(string $key, ?array $headers = null): bool|int
    {
        $headers = ($headers)
            ? $headers
            : $this->headers;

        foreach ($headers as $index => $header) {
            if ($header->getKey() == $key) {
                return $index;
            }
        }

        return false;
    }

    protected function cookieExists(string $key, ?array $cookies = null): bool|int
    {
        $cookies = ($cookies)
            ? $cookies
            : $this->cookies;

        foreach ($cookies as $index => $cookie) {
            if ($cookie->getKey() == $key) {
                return $index;
            }
        }

        return false;
    }
}
