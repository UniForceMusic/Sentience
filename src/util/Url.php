<?php

namespace src\util;

class Url
{
    public static function getBaseUrl(): string
    {
        return (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on" ? "https" : "http") . sprintf("://%s", $_SERVER["HTTP_HOST"]);
    }

    public static function getPath(): string
    {
        return trim($_SERVER["REQUEST_URI"], "/");
    }

    public static function getRequestUrl(): string
    {
        return sprintf("%s/%s", static::getBaseUrl(), static::getPath());
    }

    public static function getIndexUrl(): string
    {
        $baseUrl = static::getRequestUrl();
        $basePath = BASEDIR;

        $urlSplit = explode("/", trim($baseUrl, "/"));
        $pathSplit = explode(DIRECTORY_SEPARATOR, trim($basePath, "/\\"));

        $indexUrlParts = [static::getBaseUrl()];

        foreach ($urlSplit as $urlPart) {
            foreach ($pathSplit as $pathPart) {
                if (($urlPart == $pathPart) && !empty($urlPart)) {
                    $indexUrlParts[] = $urlPart;
                }
            }
        }

        return implode("/", $indexUrlParts);
    }

    public static function getRequestUri(): string
    {
        return trim(
            str_replace(
                static::getIndexUrl(),
                '',
                static::getRequestUrl()
            ),
            "/"
        );
    }

    public static function urlencodeParameters(array $parameters): string
    {
        $parametersArray = [];

        foreach ($parameters as $parameterName => $parameterValue) {
            $urlEncodedParameter = sprintf("%s=%s", urlencode($parameterName), urlencode($parameterValue));
            $parametersArray[] = $urlEncodedParameter;
        }

        $queryString = implode("&", $parametersArray);

        return $queryString;
    }

    public static function urldecodeParameters(string $string): array
    {
        $parts = explode("&", $string);
        $partsArray = [];

        foreach ($parts as $part) {
            $partSplit = explode("=", $part, 2);
            $key = urldecode($partSplit[0]);
            $value = urldecode($partSplit[1]);

            $partsArray[$key] = $value;
        }

        return $partsArray;
    }
}

?>