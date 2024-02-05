<?php

namespace src\filesystem;

class PageImporter
{
    public static function scanPages(string $baseDir, string $path, bool $returnAbsolute = true): array
    {
        $absolutePath = appendToBaseDir($baseDir, $path);

        return static::scanPagesRecursive($absolutePath, $returnAbsolute);
    }

    protected static function scanPagesRecursive(string $absolutePath, bool $returnAbsolute = true): array
    {
        $pages = [];

        $items = Filesystem::scandir($absolutePath, $returnAbsolute, ['.php', '.html', '.htm']);

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
