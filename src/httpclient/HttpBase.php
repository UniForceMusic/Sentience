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

    public function parameters(array $parameters, bool $replace = true): static
    {
        foreach ($parameters as $key => $value) {
            $queryParameter = new QueryParameter(
                $key,
                $value
            );

            if ($replace) {
                $this->headers = array_values(
                    array_filter(
                        $this->parameters,
                        function (QueryParameter $parameter) use ($key): bool {
                            return ($parameter->getKey() !=  $key);
                        }
                    )
                );
            }

            $this->parameters[] = $queryParameter;
        }

        return $this;
    }

    public function parameter(string $key, string $value, bool $replace = true): static
    {
        $queryParameter = new QueryParameter(
            $key,
            $value
        );

        if ($replace) {
            $this->headers = array_values(
                array_filter(
                    $this->parameters,
                    function (QueryParameter $parameter) use ($key): bool {
                        return ($parameter->getKey() !=  $key);
                    }
                )
            );
        }

        $this->parameters[] = $queryParameter;

        return $this;
    }

    public function headers(array $headers, bool $replace = true): static
    {
        foreach ($headers as $key => $value) {
            $lcKey = strtolower($key);

            $header = new Header(
                $lcKey,
                $value
            );

            if ($replace) {
                $this->headers = array_values(
                    array_filter(
                        $this->headers,
                        function (Header $header) use ($lcKey): bool {
                            return ($header->getKey() !=  $lcKey);
                        }
                    )
                );
            }

            $this->headers[] = $header;
        }

        return $this;
    }

    public function header(string $key, string $value, bool $replace = true): static
    {
        $lcKey = strtolower($key);

        $header = new Header(
            $key,
            $value
        );

        if ($replace) {
            $this->headers = array_values(
                array_filter(
                    $this->headers,
                    function (Header $header) use ($lcKey): bool {
                        return ($header->getKey() !=  $lcKey);
                    }
                )
            );
        }

        $this->headers[] = $header;

        return $this;
    }

    public function cookies(array $cookies, bool $replace = true): static
    {
        foreach ($cookies as $key => $value) {
            $cookie = new Cookie(
                $key,
                $value
            );

            if ($replace) {
                $this->cookies = array_values(
                    array_filter(
                        $this->cookies,
                        function (Cookie $cookie) use ($key): bool {
                            return ($cookie->getKey() !=  $key);
                        }
                    )
                );
            }

            $this->cookies[] = $cookie;
        }

        return $this;
    }

    public function cookie(string $key, string $value, bool $replace = true): static
    {
        $cookie = new Cookie(
            $key,
            $value
        );

        if ($replace) {
            $this->cookies = array_values(
                array_filter(
                    $this->cookies,
                    function (Cookie $cookie) use ($key): bool {
                        return ($cookie->getKey() !=  $key);
                    }
                )
            );
        }

        $this->cookies[] = $cookie;

        return $this;
    }

    public function setCookieStrings(string $cookieString, bool $replace = true): static
    {
        $key = 'cookie';

        $header = new Header(
            $key,
            $cookieString
        );

        if ($replace) {
            $this->headers = array_values(
                array_filter(
                    $this->headers,
                    function (Header $header) use ($key): bool {
                        return ($header->getKey() !=  $key);
                    }
                )
            );
        }

        if (!$this->headerExists($key)) {
            $this->headers[] = $header;
        }

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

            $queryParameter = new QueryParameter(
                $key,
                $value
            );

            $parameters[] = $queryParameter;
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
