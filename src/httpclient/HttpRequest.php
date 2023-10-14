<?php

namespace src\httpclient;

use src\exceptions\UrlException;
use src\util\MimeTypes;

class HttpRequest
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
    protected array $cookies;
    protected string|array $body;
    protected bool $secure;
    protected int $retryCount;
    protected array $customOptions;

    public function __construct()
    {
        $this->url = '';
        $this->method = $this::GET;
        $this->parameters = [];
        $this->headers = [];
        $this->cookies = [];
        $this->body = '';
        $this->secure = true;
        $this->retryCount = 0;
        $this->customOptions = [];
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
                $index = $this->parameterExists($key);

                if ($index) {
                    $this->parameters[$index] = $queryParameter;
                    continue;
                }
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
            $index = $this->parameterExists($key);

            if ($index) {
                $this->parameters[$index] = $queryParameter;
            }
        }

        $this->parameters[] = $queryParameter;

        return $this;
    }

    public function headers(array $headers, bool $replace = true): static
    {
        foreach ($headers as $key => $value) {
            $header = new Header(
                $key,
                $value
            );

            if ($replace) {
                $index = $this->headerExists($key);

                if ($index) {
                    $this->headers[$index] = $header;
                    continue;
                }
            }

            $this->headers[] = $header;
        }

        return $this;
    }

    public function header(string $key, string $value, bool $replace = true): static
    {
        $header = new Header(
            $key,
            $value
        );

        if ($replace) {
            $index = $this->headerExists($key);

            if ($index) {
                $this->headers[$index] = $header;
            }
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
                $index = $this->cookieExists($key);

                if ($index) {
                    $this->cookies[$index] = $cookie;
                    continue;
                }
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
            $index = $this->cookieExists($key);

            if ($index) {
                $this->cookies[$index] = $cookie;
            }
        }

        $this->cookies[] = $cookie;

        return $this;
    }

    public function setCookieStrings(string $cookieString, bool $replace = true): static
    {
        $key = 'Cookie';

        $header = new Header(
            $key,
            $cookieString
        );

        if ($replace) {
            $index = $this->headerExists($key);

            if ($index) {
                $this->headers[$index] = $header;
            }

            $this->cookies = [];
        }

        $this->headers[] = $header;

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

    public function execute(): HttpResponse
    {
        return HttpClient::executeRequest(
            $this->url,
            $this->method,
            $this->parameters,
            $this->headers,
            $this->cookies,
            $this->body,
            $this->secure,
            $this->retryCount,
            $this->customOptions
        );
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

    protected function parameterExists(string $key): bool|int
    {
        foreach ($this->parameters as $index => $parameter) {
            if ($parameter->getKey() == $key) {
                return $index;
            }
        }

        return false;
    }

    protected function headerExists(string $key): bool|int
    {
        foreach ($this->headers as $index => $header) {
            if ($header->getKey() == $key) {
                return $index;
            }
        }

        return false;
    }

    protected function cookieExists(string $key): bool|int
    {
        foreach ($this->cookies as $index => $cookie) {
            if ($cookie->getKey() == $key) {
                return $index;
            }
        }

        return false;
    }
}
