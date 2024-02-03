<?php

namespace src\filesystem;

use src\exceptions\FilesystemException;

class Filesystem
{
    public static function scandir(string $absolutePath, bool $returnAbsolute = true): array
    {
        if (!file_exists($absolutePath)) {
            throw new FilesystemException(sprintf('file/dir %s does not exist', $absolutePath));
        }

        $items = array_filter(
            scandir($absolutePath),
            function (string $item) {
                return !in_array($item, ['.', '..']);
            }
        );

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

    public static function scandirRecursive(string $absolutePath, bool $returnAbsolute = true): array
    {
        $paths = [];

        if (!file_exists($absolutePath)) {
            throw new FilesystemException(sprintf('file/dir %s does not exist', $absolutePath));
        }

        $items = array_filter(
            scandir($absolutePath),
            function (string $item) {
                return !in_array($item, ['.', '..']);
            }
        );

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
                        $absolutePath,
                        '',
                        $item
                    );
                },
                $paths
            );
        }

        return array_values($paths);
    }

    protected static function scandirRecursiveScanner(string $absolutePath): array
    {
        $paths = [];

        $items = array_filter(
            scandir($absolutePath),
            function (string $item) {
                return !in_array($item, ['.', '..']);
            }
        );

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
}
