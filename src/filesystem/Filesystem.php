<?php

namespace src\filesystem;

use src\exceptions\FilesystemException;

class Filesystem
{
    public static function scandir(string $absolutePath, bool $returnAbsolute = true, ?array $allowedFileExtensions = null): array
    {
        if (!file_exists($absolutePath)) {
            throw new FilesystemException(sprintf('directory %s does not exist', $absolutePath));
        }

        $items = static::scandirSortedAlphabetical($absolutePath);

        if ($allowedFileExtensions) {
            $items = static::filterFileExtensions($items, $allowedFileExtensions);
        }

        $absolutePaths = array_map(
            function (string $item) use ($absolutePath, $returnAbsolute): string {
                if ($returnAbsolute) {
                    return appendToBaseDir($absolutePath, $item);
                }

                return $item;
            },
            $items
        );

        return array_values($absolutePaths);
    }

    public static function scandirRecursive(string $absolutePath, bool $returnAbsolute = true, ?array $allowedFileExtensions = null): array
    {
        $paths = [];

        if (!file_exists($absolutePath)) {
            throw new FilesystemException(sprintf('directory %s does not exist', $absolutePath));
        }

        $items = static::scandirSortedAlphabetical($absolutePath);

        foreach ($items as $item) {
            $absoluteItemPath = appendToBaseDir($absolutePath, $item);

            if (is_file($absoluteItemPath)) {
                $paths[] = $absoluteItemPath;
            }

            if (is_dir($absoluteItemPath)) {
                $resurciveItems = static::scandirRecursiveScanner($absoluteItemPath);

                foreach ($resurciveItems as $resurciveItem) {
                    $paths[] = $resurciveItem;
                }
            }
        }

        if (!$returnAbsolute) {
            $paths = array_map(
                function (string $item) use ($absolutePath): string {
                    return str_replace(
                        sprintf('%s%s', $absolutePath, DIRECTORY_SEPARATOR),
                        '',
                        $item
                    );
                },
                $paths
            );
        }

        if ($allowedFileExtensions) {
            $paths = static::filterFileExtensions($paths, $allowedFileExtensions);
        }

        return array_values($paths);
    }

    protected static function scandirRecursiveScanner(string $absolutePath): array
    {
        $paths = [];

        $items = static::scandirSortedAlphabetical($absolutePath);

        foreach ($items as $item) {
            $absoluteItemPath = appendToBaseDir($absolutePath, $item);

            if (is_file($absoluteItemPath)) {
                $paths[] = $absoluteItemPath;
            }

            if (is_dir($absoluteItemPath)) {
                $resurciveItems = static::scandirRecursiveScanner($absoluteItemPath);

                foreach ($resurciveItems as $resurciveItem) {
                    $paths[] = $resurciveItem;
                }
            }
        }

        return array_values($paths);
    }

    protected static function scandirSortedAlphabetical(string $path, $includeDirectories = true)
    {
        $items = scandir($path);

        sort($items);

        return array_values(
            array_filter(
                $items,
                function (string $item) use ($path, $includeDirectories): bool {
                    if (!$includeDirectories && is_dir(appendToBaseDir($path, $item))) {
                        return false;
                    }

                    return !in_array($item, ['.', '..']);
                }
            )
        );
    }

    protected static function filterFileExtensions(array $items, array $allowedFileExtensions): array
    {
        return array_values(
            array_filter(
                $items,
                function (string $item) use ($allowedFileExtensions): bool {
                    $lcItem = strtolower($item);

                    foreach ($allowedFileExtensions as $fileExtension) {
                        $lcFileExtension = strtolower($fileExtension);

                        if (str_ends_with($lcItem, $lcFileExtension)) {
                            return true;
                        }
                    }

                    return false;
                }
            )
        );
    }
}
