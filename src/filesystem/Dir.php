<?php

namespace src\filesystem;

use src\exceptions\FilesystemException;

class Dir
{
    public static function scan(string $dir, bool $returnAbsolute = false): array
    {
        return Filesystem::scandir($dir, $returnAbsolute);
    }

    public static function scanRecursive(string $dir, bool $returnAbsolute = false): array
    {
        return Filesystem::scandirRecursive($dir, $returnAbsolute);
    }

    public static function create(string $dir, int $permissions = 777): void
    {
        $success = mkdir($dir, $permissions, true);

        if (!$success) {
            throw new FilesystemException(sprintf('error creating dir "%s"', $dir));
        }
    }

    public static function delete(string $dir): void
    {
        $items = static::scanRecursive($dir, true);

        $dirsToRemove = [];

        foreach ($items as $item) {
            if (is_file($item)) {
                File::delete($item);
            }

            if (is_dir($item)) {
                $dirsToRemove[] = $dirsToRemove;
            }
        }

        foreach ($dirsToRemove as $dirToRemove) {
            rmdir($dirToRemove);
        }
    }
}
