<?php

namespace src\filesystem;

use src\util\Strings;

class PageImporter
{
    public static function scanPages(string $baseDir, string $path): array
    {
        $absolutePath = appendToBaseDir($baseDir, $path);

        $allowedFileExtensions = ['.php', '.html', '.htm'];

        $scannedPages = Filesystem::scandirRecursive(
            $absolutePath,
            true,
            $allowedFileExtensions
        );

        $pages = [];

        foreach ($scannedPages as $page) {
            $path = Strings::strip($absolutePath, $page);
            $path = Strings::beforeSubstr($path, '.');

            if (is_file($page) && str_starts_with(basename($page), 'index.')) {
                $path = rtrim($path, 'index');
            }

            $pages[$page] = $path;
        }

        return $pages;
    }
}
