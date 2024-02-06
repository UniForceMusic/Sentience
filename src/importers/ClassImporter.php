<?php

namespace src\importers;

use src\filesystem\Filesystem;
use src\util\Strings;

class ClassImporter
{
    public static function importAsString(string $baseDir, string $path, array $exclude = []): ?array
    {
        $dirItems = Filesystem::scandir(
            appendToBaseDir($baseDir, $path),
            false,
            ['.php']
        );

        $classNames = array_map(
            function (string $item) use ($path) {
                $namespacePath = str_replace(['/', '\\'], '\\', $path);
                $className = Strings::beforeSubstr($item, '.');

                return trim(
                    sprintf('%s\\%s', $namespacePath, $className),
                    '\\'
                );
            },
            $dirItems
        );

        $filteredClassNames = array_filter(
            $classNames,
            function (string $className) use ($exclude) {
                return !in_array($className, $exclude);
            }
        );

        return array_values($filteredClassNames);
    }

    public static function importAsClass(string $baseDir, string $path, array $exclude = [], array $args = []): ?array
    {
        $classNames = static::importAsString($baseDir, $path, $exclude);

        $classes = array_map(
            function (string $className) use ($args) {
                return new $className(...$args);
            },
            $classNames
        );

        return array_values($classes);
    }
}
