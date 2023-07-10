<?php

use src\util\Strings;

class DotEnv
{
    public static function parseFile(string $filePath)
    {
        if (!file_exists($filePath)) {
            static::createFile($filePath);
            return;
        }

        $fileContents = file_get_contents($filePath);

        $variables = static::parseString($fileContents);

        foreach ($variables as $key => $value) {
            $_ENV[$key] = static::parseVariable($value);
        }
    }

    protected static function createFile(string $filePath)
    {
        file_put_contents($filePath, '');
    }

    protected static function parseString(string $string): array
    {
        $newline = Strings::detectNewline($string);
        $lines = explode($newline, $string);

        $variables = [];

        foreach ($lines as $line) {
            $match = preg_match('/(.*)=(.[^#]*)/', $line, $matches);
            if (!$match) {
                continue;
            }

            if (count($matches) < 3) {
                continue;
            }

            $key = $matches[1];
            $value = $matches[2];

            $variables[$key] = $value;
        }

        return $variables;
    }

    protected static function parseVariable(string $value): mixed
    {
        $trimmedValue = trim($value);

        if (substr($trimmedValue, 0, 1) == '"') {
            return static::parseStringValue($trimmedValue);
        }

        if (in_array(strtolower($trimmedValue), ['true', 'false'])) {
            return static::parseBoolValue($trimmedValue);
        }

        if (str_contains($trimmedValue, '.')) {
            return static::parseFloatValue($trimmedValue);
        }

        if (preg_match('/^[0-9]*/', $trimmedValue)) {
            return static::parseIntValue($trimmedValue);
        }

        return null;
    }

    protected static function parseStringValue(string $value): string
    {
        $quoteTrim = trim($value, '"');
        $replaceEscapedQuotes = str_replace('\"', '"', $quoteTrim);

        return $replaceEscapedQuotes;
    }

    protected static function parseBoolValue(string $value): ?bool
    {
        return (strtolower($value) == 'true') ? true : false;
    }

    protected static function parseIntValue(string $value): int
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    protected static function parseFloatValue(string $value): float
    {
        return (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
    }
}

DotEnv::parseFile(BASEDIR . '/.env');

?>