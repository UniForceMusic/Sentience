<?php

namespace src\util;

class FormData
{
    public static function encode(?array $data, bool $unique = true): ?string
    {
        if (is_null($data)) {
            return null;
        }

        $serialized = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($unique) {
                    $serialized[$key] = end($value);
                    continue;
                }

                foreach ($value as $k => $v) {
                    $serialized[] = sprintf(
                        '%s=%s',
                        urlencode($k),
                        urlencode($v)
                    );
                }
                continue;
            }

            $serialized[] = sprintf(
                '%s=%s',
                urlencode($key),
                urlencode($value)
            );
        }

        return implode('&', $serialized);
    }

    public static function decode(?string $string, bool $unique = true): ?array
    {
        if (is_null($string)) {
            return null;
        }

        $pairs = explode('&', $string);
        $unserialized = [];

        foreach ($pairs as $pair) {
            if (empty($pair)) {
                continue;
            }

            $pairSplit = explode('=', $pair, 2);

            $key = urldecode($pairSplit[0]);
            $value = urldecode($pairSplit[1] ?? '');

            if (!key_exists($key, $unserialized)) {
                $unserialized[$key] = [];
            }

            if (!str_contains($pair, '=')) {
                $unserialized[$key][] = '';
                continue;
            }

            $unserialized[$key][] = $value;
        }

        if ($unique) {
            return array_map(
                function (array $values) {
                    return (count($values) > 1)
                        ? end($values)
                        : $values[0];
                },
                $unserialized
            );
        }

        return array_map(
            function (array $values) {
                return (count($values) < 2)
                    ? $values[0]
                    : $values;
            },
            $unserialized
        );
    }
}
