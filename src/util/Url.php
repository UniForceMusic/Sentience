<?php

namespace src\util;

class Url
{
    public static function getBaseUrl(): string
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . sprintf('://%s', $_SERVER['HTTP_HOST']);
    }

    public static function getPath(): string
    {
        $requestUri = $_SERVER['REQUEST_URI'];

        if (str_contains($requestUri, '?')) {
            return trim(
                strstr(
                    $requestUri,
                    '?',
                    true
                ),
                '/'
            );
        }

        return trim($requestUri, '/');
    }

    public static function getRequestUrl(): string
    {
        return sprintf('%s/%s', static::getBaseUrl(), static::getPath());
    }

    public static function getIndexUrl(): string
    {
        $host = $_SERVER['HTTP_HOST'];
        $baseUrl = static::getRequestUrl();
        $basePath = BASEDIR;

        $urlSplit = explode('/', trim($baseUrl, '/'));
        $pathSplit = explode(DIRECTORY_SEPARATOR, trim($basePath, '/\\'));

        $indexUrlParts = [static::getBaseUrl()];

        foreach ($urlSplit as $urlPart) {
            foreach ($pathSplit as $pathPart) {
                /**
                 * Prevent the index url matching the www directory in hosts like Plesk
                 */
                if ($pathPart == $host) {
                    continue;
                }

                $partsMatch = (($urlPart == $pathPart) && !empty($urlPart));

                if ($partsMatch) {
                    $indexUrlParts[] = $urlPart;
                }
            }
        }

        return implode('/', $indexUrlParts);
    }

    public static function getRequestUri(): string
    {
        return trim(
            str_replace(
                static::getIndexUrl(),
                '',
                static::getRequestUrl()
            ),
            '/'
        );
    }

    public static function urlencodeParameters(array $parameters): string
    {
        $parametersArray = [];

        foreach ($parameters as $parameterName => $parameterValue) {
            $urlEncodedParameter = sprintf('%s=%s', urlencode($parameterName), urlencode($parameterValue));
            $parametersArray[] = $urlEncodedParameter;
        }

        $queryString = implode('&', $parametersArray);

        return $queryString;
    }

    public static function urldecodeParameters(string $string): array
    {
        $parts = explode('&', $string);
        $partsArray = [];

        foreach ($parts as $part) {
            $partSplit = explode('=', $part, 2);
            $key = urldecode($partSplit[0]);
            $value = urldecode($partSplit[1]);

            $partsArray[$key] = $value;
        }

        return $partsArray;
    }
}
