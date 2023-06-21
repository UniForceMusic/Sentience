<?php

namespace src\httpclient;

use CurlHandle;

class HttpRequest
{
    public static function get(string $url, array $parameters = [], array $headers = [], bool $secure = true, array $customOptions = [])
    {
        return static::custom(
            $url,
            'GET',
            $parameters,
            $headers,
            '',
            $secure,
            $customOptions
        );
    }

    public static function post(string $url, array $parameters = [], array $headers = [], string|array $body = '', bool $secure = true, array $customOptions = [])
    {
        return static::custom(
            $url,
            'POST',
            $parameters,
            $headers,
            $body,
            $secure,
            $customOptions
        );
    }

    public static function put(string $url, array $parameters = [], array $headers = [], string|array $body = '', bool $secure = true, array $customOptions = [])
    {
        return static::custom(
            $url,
            'PUT',
            $parameters,
            $headers,
            $body,
            $secure,
            $customOptions
        );
    }

    public static function patch(string $url, array $parameters = [], array $headers = [], string|array $body = '', bool $secure = true, array $customOptions = [])
    {
        return static::custom(
            $url,
            'PATCH',
            $parameters,
            $headers,
            $body,
            $secure,
            $customOptions
        );
    }

    public static function delete(string $url, array $parameters = [], array $headers = [], string|array $body = '', bool $secure = true, array $customOptions = [])
    {
        return static::custom(
            $url,
            'DELETE',
            $parameters,
            $headers,
            $body,
            $secure,
            $customOptions
        );
    }

    public static function custom(string $url, string $method, array $parameters = [], array $headers = [], string|array $body = '', bool $secure = true, array $customOptions = [])
    {
        $curl = curl_init();
        $url = static::serializeParameters($url, $parameters);
        $headers = static::serializeHeaders($headers);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, ($method != 'GET'));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        foreach ($customOptions as $curlOpt => $value) {
            curl_setopt($curl, $curlOpt, $value);
        }

        if (!$secure) {
            $curl = static::setCurlInsecure($curl);
        }

        return new HttpResponse($curl);
    }

    private static function serializeParameters(string $url, array $parameters)
    {
        if (!empty($parameters)) {
            $parametersArray = [];

            foreach ($parameters as $parameterName => $parameterValue) {
                $urlEncodedParameter = sprintf(
                    '%s=%s',
                    urlencode($parameterName),
                    urlencode($parameterValue)
                );
                $parametersArray[] = $urlEncodedParameter;
            }

            $queryString = implode('&', $parametersArray);
            $url = sprintf('%s?%s', $url, $queryString);
        }

        return $url;
    }

    private static function serializeHeaders(array $headers)
    {
        if (!empty($headers)) {
            $headersArray = [];

            foreach ($headers as $headerName => $headerValue) {
                $urlEncodedHeader = sprintf(
                    '%s: %s',
                    $headerName,
                    $headerValue
                );
                $headersArray[] = $urlEncodedHeader;
            }

            $headers = $headersArray;
        }

        return $headers;
    }

    private static function setCurlInsecure(CurlHandle $curl)
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        return $curl;
    }
}

?>