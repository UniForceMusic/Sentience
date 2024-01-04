<?php

namespace src\filesystem;

use src\exceptions\FilesystemException;

class ClassImporter
{
    public static function importAsString(string $baseDir, string $path, array $exclude = []): ?array
    {
        $absolutePath = appendToBaseDir($baseDir, $path);

        if (!file_exists($absolutePath)) {
            throw new FilesystemException(sprintf('file %s does not exist', $absolutePath));
        }

        $dirItems = array_filter(
            scandir($absolutePath),
            function (string $item) {
                return (!in_array($item, ['.', '..']) && str_ends_with($item, '.php'));
            }
        );

        $classNames = array_map(
            function (string $item) use ($path) {
                $namespacePath = str_replace(['/', '\\'], '\\', $path);
                $className = explode('.', $item)[0];

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
