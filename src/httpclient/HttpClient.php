<?php

namespace src\httpclient;

use CurlHandle;

class HttpClient
{
    public static function new(): HttpRequest
    {
        return new HttpRequest();
    }

    public static function executeRequest(string $url, string $method, array $parameters, array $headers, array $cookies, string|array $body, bool $secure, int $retryCount, array $customOptions): HttpResponse
    {
        $curl = curl_init();
        $url = static::serializeParameters($url, $parameters);
        $headers = static::serializeHeaders($headers, $cookies);

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

        return new HttpResponse($curl, $retryCount);
    }

    protected static function setCurlInsecure(CurlHandle $curl): CurlHandle
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        return $curl;
    }

    protected static function serializeParameters(string $url, array $parameters): string
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

    protected static function serializeHeaders(array $headers, array $cookies): array
    {
        if (empty($headers)) {
            return [];
        }

        $serializedHeaders = [];

        foreach ($headers as $header) {
            $serializedHeaders[] = $header->getHeaderString();
        }

        if (!empty($cookies) && !static::headerExists($headers, 'Cookie')) {
            $serializedHeaders[] = sprintf(
                'Cookie: %s',
                static::serializeCookies($cookies)
            );
        }

        return $serializedHeaders;
    }

    protected static function serializeCookies(array $cookies): string
    {
        $cookieStrings = [];

        foreach ($cookies as $cookie) {
            $cookieStrings[] = $cookie->getCookieString();
        }

        return implode(';', $cookieStrings);
    }

    protected static function headerExists(array $headers, string $key): bool|int
    {
        foreach ($headers as $index => $header) {
            if ($header->getKey() == $key) {
                return $index;
            }
        }

        return false;
    }
}
