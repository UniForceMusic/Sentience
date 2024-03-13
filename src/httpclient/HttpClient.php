<?php

namespace src\httpclient;

use CurlHandle;
use src\util\Methods;
use src\util\Strings;

class HttpClient extends HttpBase
{
    public function createRequest(): HttpRequest
    {
        $httpRequest = new HttpRequest($this);

        if ($this->baseUrl) {
            $httpRequest->baseUrl($this->baseUrl);
        }

        if ($this->path) {
            $httpRequest->path($this->path);
        }

        if ($this->url) {
            $httpRequest->url($this->url);
        }

        if ($this->method) {
            $httpRequest->method($this->method);
        }

        if ($this->parameters) {
            $httpRequest->parameters($this->parameters);
        }

        if ($this->headers) {
            $httpRequest->headers($this->headers);
        }

        if ($this->cookies) {
            $httpRequest->cookies($this->cookies);
        }

        if ($this->body) {
            $httpRequest->body($this->body);
        }

        if ($this->secure) {
            $httpRequest->secure($this->secure);
        }

        if ($this->retryCount) {
            $httpRequest->retryCount($this->retryCount);
        }

        if ($this->customOptions) {
            $httpRequest->customOptions($this->customOptions);
        }

        return $httpRequest;
    }

    public function execute(
        string $url,
        string $method,
        array $parameters,
        array $headers,
        array $cookies,
        null|string|array $body,
        bool $secure,
        int $retryCount,
        array $customOptions
    ): HttpResponse {
        $curl = curl_init();
        $url = $this->serializeParameters($url, $parameters);
        $headers = $this->serializeHeaders($headers, $cookies);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        if ($method != Methods::GET) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, ($body ?? ''));
        }

        if (!$secure) {
            $curl = $this->setCurlInsecure($curl);
        }

        foreach ($customOptions as $curlOpt => $value) {
            curl_setopt($curl, $curlOpt, $value);
        }

        return new HttpResponse($curl, $retryCount);
    }

    protected function setCurlInsecure(CurlHandle $curl): CurlHandle
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        return $curl;
    }

    protected function serializeParameters(string $url, array $parameters): string
    {
        if (empty($parameters)) {
            return $url;
        }

        $serializedParameters = [];

        foreach ($parameters as $key => $value) {
            $serializedParameters[] = $this->serializeParameter($key, $value);
        }

        $queryString = implode('&', $serializedParameters);

        return sprintf('%s?%s', $url, $queryString);
    }

    protected function serializeParameter(string $key, string|array $value): string
    {
        if (is_array($value)) {
            return implode(
                '&',
                array_map(
                    function (string $v) use ($key) {
                        $this->serializeParameter($key, $v);
                    },
                    $value
                )
            );
        }

        return sprintf(
            '%s=%s',
            urlencode($key),
            urlencode($value)
        );
    }

    protected function serializeHeaders(array $headers, array $cookies): array
    {
        if (empty($headers)) {
            return [];
        }

        $serializedHeaders = [];

        if (!empty($cookies)) {
            $this->header('cookie', $this->serializeCookies($cookies));
        }

        foreach ($headers as $key => $value) {
            $serializedHeaders[] = $this->serializeHeader($key, $value);
        }

        return $serializedHeaders;
    }

    protected function serializeHeader(string $key, string|array $value): string
    {
        return sprintf(
            '%s: %s',
            replaceNonAsciiChars($key),
            replaceNonAsciiChars(Strings::join(', ', $value))
        );
    }

    protected function serializeCookies(array $cookies): string
    {
        $cookieStrings = [];

        foreach ($cookies as $key => $value) {
            $cookieStrings[] = $this->serializeCookie($key, $value);
        }

        return implode(';', $cookieStrings);
    }

    protected function serializeCookie(string $key, string $value): string
    {
        return sprintf(
            '%s=%s',
            replaceNonAsciiChars($key),
            replaceNonAsciiChars($value)
        );
    }
}
