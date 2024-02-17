<?php

namespace src\util;

use JsonException;

class Json
{
    public static function encode(mixed $data, bool $prettyPrint = false): string
    {
        $flags = ($prettyPrint)
            ? JSON_PRETTY_PRINT
            : 0;

        return json_encode($data, $flags);
    }

    public static function decode(string $json): ?array
    {
        if (empty($json)) {
            return null;
        }

        $decoded = json_decode($json, true);

        if (is_null($decoded) && json_last_error_msg() != 'No error') {
            throw new JsonException(json_last_error_msg());
        }

        return $decoded;
    }
}
