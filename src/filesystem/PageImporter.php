<?php

namespace src\filesystem;

use src\exceptions\FilesystemException;

class PageImporter
{
    public static function scanPages(string $baseDir, string $path): array
    {
        $absolutePath = appendToBaseDir($baseDir, $path);

        $paths = static::scanPagesRecursive($absolutePath);

        return array_map(
            function (string $path) use ($absolutePath): string {
                return str_replace(
                    $absolutePath,
                    '',
                    $path
                );
            },
            $paths
        );
    }

    protected static function scanPagesRecursive(string $absolutePath): array
    {
        $pages = [];

        $items = Filesystem::scandir($absolutePath);

        foreach ($items as $item) {
            if (is_file($item)) {
                $pages[] = $item;
            }

            if (is_dir($item)) {
                $resurciveItems = static::scanPagesRecursive($item);

                foreach ($resurciveItems as $resurciveItem) {
                    $pages[] = $resurciveItem;
                }
            }
        }

        return $pages;
    }
}
