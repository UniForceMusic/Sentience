<?php

namespace src\httpclient;

use CurlHandle;
use src\exceptions\InvalidURLException;

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
        if (!parse_url($url)) {
            throw new InvalidURLException(sprintf('Url: "%s" is invalid', $url));
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
            strtolower($method)
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
                $index = $this->parameterExists($key);

                if ($index) {
                    $this->headers[$index] = $header;
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
            $index = $this->parameterExists($key);

            if ($index) {
                $this->headers[$index] = $header;
            }
        }

        $this->headers[] = $header;

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
        return $this->createRequest(
            $this->url,
            $this->method,
            $this->parameters,
            $this->headers,
            $this->body,
            $this->secure,
            $this->customOptions
        );
    }

    protected function createRequest(string $url, string $method, array $parameters, array $headers, string|array $body, bool $secure, array $customOptions)
    {
        $curl = curl_init();
        $url = $this->serializeParameters($url, $parameters);
        $headers = static::serializeHeaders($headers);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        if ($method != 'GET') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }

        if (!$secure) {
            $curl = static::setCurlInsecure($curl);
        }

        foreach ($customOptions as $curlOpt => $value) {
            curl_setopt($curl, $curlOpt, $value);
        }

        return new HttpResponse($curl);
    }

    protected function setCurlInsecure(CurlHandle $curl)
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        return $curl;
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

    protected function serializeParameters(string $url, array $parameters): string
    {
        if (empty($parameters)) {
            return $url;
        }

        $serializedParameters = [];

        foreach ($parameters as $parameter) {
            $serializedParameters[] = $parameter->getQueryString();
        }

        $queryString = implode('&', $serializedParameters);
        return sprintf('%s?%s', $url, $queryString);
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

    protected function serializeHeaders(array $headers): array
    {
        if (empty($this->headers)) {
            return [];
        }

        $serializedHeaders = [];

        foreach ($headers as $header) {
            $serializedHeaders[] = $header->getHeaderString();
        }

        return $serializedHeaders;
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
}

?>