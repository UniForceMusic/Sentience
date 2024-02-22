<?php

namespace src\util;

class Data
{
    public static function get(null|array|object $data, string $key, mixed $default = null): mixed
    {
        if (is_null($data)) {
            return $default;
        }

        if ($key == '') {
            return $data;
        }

        $pointers = explode('.', $key);

        $current = (array) $data;

        foreach ($pointers as $pointer) {
            if (!key_exists($pointer, $current)) {
                return $default;
            }

            $current = $current[$pointer];
        }

        return $current;
    }

    public static function exists(null|array|object $data, string $key): bool
    {
        if (is_null($data)) {
            return false;
        }

        if ($key == '') {
            return true;
        }

        $pointers = explode('.', $key);
        $pointerCount = count($pointers);

        $current = (array) $data;

        foreach ($pointers as $index => $pointer) {
            if ($index == $pointerCount - 1) {
                return true;
            }

            if (!key_exists($pointer, $current)) {
                return false;
            }

            $current = $current[$pointer];
        }

        return true;
    }

    public static function isArray(?array $array): bool
    {
        if (is_null($array)) {
            return false;
        }

        $counter = 0;

        foreach ($array as $key => $value) {
            if ($key != strval($counter)) {
                return false;
            }

            $counter++;
        }

        return true;
    }
}
