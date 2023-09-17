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
}
