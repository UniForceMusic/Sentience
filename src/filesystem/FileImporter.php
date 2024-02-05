<?php

namespace src\filesystem;

use src\util\Strings;

class FileImporter
{
    public static function scanFiles(string $baseDir, string $path): array
    {
        $absolutePath = appendToBaseDir($baseDir, $path);

        $scannedFiles = Filesystem::scandirRecursive($absolutePath);

        $files = [];

        foreach ($scannedFiles as $file) {
            $files[$file] = Strings::strip($absolutePath, $file);
        }

        return $files;
    }
}
