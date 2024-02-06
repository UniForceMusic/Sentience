<?php

use src\util\Strings;

class DotEnv
{
    public static function loadFile(string $filePath, ?string $exampleFilePath = null): void
    {
        if (!file_exists($filePath)) {
            static::createFile($filePath, $exampleFilePath);
        }

        $variables = static::parseFile($filePath);

        foreach ($variables as $key => $value) {
            $_ENV[$key] = $value;
        }
    }

    public static function parseFile(string $filePath): array
    {
        $fileContents = file_get_contents($filePath);

        $variables = static::parseString($fileContents);

        return array_map(
            function (mixed $value): mixed {
                return static::parseVariable($value);
            },
            $variables
        );
    }

    public static function parseFileRaw(string $filePath): array
    {
        $fileContents = file_get_contents($filePath);

        return static::parseString($fileContents);
    }

    protected static function createFile(string $filePath, ?string $exampleFilePath): void
    {
        ($exampleFilePath)
            ? copy($exampleFilePath, $filePath)
            : file_put_contents($filePath, '');
    }

    protected static function parseString(string $string): array
    {
        $newline = Strings::detectNewline($string);
        $lines = explode($newline, $string);

        $variables = [];

        foreach ($lines as $line) {
            $dotEnvRegex = "/(?:^|^)\s*(?:export\s+)?([\w.-]+)(?:\s*=\s*?|:\s+?)(\s*'(?:\\'|[^'])*'|\s*\"(?:\\\"|[^\"])*\"|\s*`(?:\\`|[^`])*`|[^#\r\n]+)?\s*(?:#.*)?(?:$|$)/";
            $match = preg_match($dotEnvRegex, $line, $matches);
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

        if (substr($trimmedValue, 0, 1) == '[') {
            return static::parseArrayValue($trimmedValue);
        }

        if (substr($trimmedValue, 0, 1) == "'") {
            return static::parseStringValue($trimmedValue, "'");
        }

        if (substr($trimmedValue, 0, 1) == '"') {
            return static::parseTemplateValue($trimmedValue);
        }

        if (in_array(strtolower($trimmedValue), ['true', 'false'])) {
            return static::parseBoolValue($trimmedValue);
        }

        if ($trimmedValue == 'null') {
            return static::parseNullValue($trimmedValue);
        }

        if (str_contains($trimmedValue, '.')) {
            return static::parseFloatValue($trimmedValue);
        }

        if (preg_match('/^[0-9]*/', $trimmedValue)) {
            return static::parseIntValue($trimmedValue);
        }

        return null;
    }

    protected static function parseArrayValue(string $value): array
    {
        $jsonRegex = '/(\"(.*?)\")|(\'(.*?)\')|[-\w.]+/';

        $values = [];

        $match = preg_match_all($jsonRegex, $value, $matches, PREG_UNMATCHED_AS_NULL);
        if (!$match) {
            return $values;
        }

        return array_map(
            function (string $value): null|bool|int|float|string {
                return static::parseVariable($value);
            },
            $matches[0]
        );
    }

    protected static function parseStringValue(string $value, string $quote): string
    {
        $quoteTrim = substr($value, 1, (strlen($value) - 2));

        return str_replace(
            sprintf('\%s', $quote),
            $quote,
            $quoteTrim
        );
    }

    protected static function parseTemplateValue(string $value): string
    {
        $string = static::parseStringValue($value, '"');

        $envTemplateRegex = '/\${(.[^\}]*)}/';
        $match = preg_match_all($envTemplateRegex, $string, $matches);
        if (!$match) {
            return $string;
        }

        return preg_replace_callback(
            $envTemplateRegex,
            function (array $matches) {
                $original = $matches[0];
                $envVarName = $matches[1];

                if (isset($_ENV[$envVarName])) {
                    return $_ENV[$envVarName];
                }

                return $original;
            },
            $string
        );
    }

    protected static function parseBoolValue(string $value): bool
    {
        return (strtolower($value) == 'true') ? true : false;
    }

    protected static function parseNullValue(string $value): mixed
    {
        return null;
    }

    protected static function parseFloatValue(string $value): float
    {
        return (float) $value;
    }

    protected static function parseIntValue(string $value): int
    {
        return (int) $value;
    }
}

DotEnv::loadFile(
    appendToBaseDir(BASEDIR, '/.env'),
    appendToBaseDir(BASEDIR, '/.env.example')
);
