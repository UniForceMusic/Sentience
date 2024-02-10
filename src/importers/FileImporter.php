<?php

namespace src\importers;

use src\filesystem\Filesystem;
use src\util\Strings;

class FileImporter
{
    public static function scanFiles(string $baseDir, string $path): array
    {
        $absolutePath = appendToBaseDir($baseDir, $path);

        $scannedFiles = Filesystem::scandirRecursive($absolutePath);

        $files = [];

        foreach ($scannedFiles as $file) {
            if (is_dir($file)) {
                continue;
            }

            $files[$file] = Strings::strip($absolutePath, $file);
        }

        return $files;
    }
}
