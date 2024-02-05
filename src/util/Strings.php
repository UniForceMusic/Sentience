<?php

namespace src\util;

class Strings
{
    public static function detectNewline(string $string): string
    {
        if (str_contains($string, "\r\n")) {
            return "\r\n";
        } else {
            return "\n";
        }
    }

    public static function strip(string $pattern, string $subject): string
    {
        return str_replace($pattern, '', $subject);
    }

    public static function join(string $glue, string|array $elements): string
    {
        if (is_string($elements)) {
            return $elements;
        }

        return implode(
            $glue,
            $elements
        );
    }

    public static function beforeSubstr(string $string, string $separator): string
    {
        if (!str_contains($string, $separator)) {
            return $string;
        }

        return explode($separator, $string, 2)[0];
    }

    public static function afterSubstr(string $string, string $separator): string
    {
        if (!str_contains($string, $separator)) {
            return $string;
        }

        return explode($separator, $string, 2)[1];
    }
}
