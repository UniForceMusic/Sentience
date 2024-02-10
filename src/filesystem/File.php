<?php

namespace src\filesystem;

use src\exceptions\FilesystemException;

class File
{
    public static function read(string $file): string
    {
        $contents = file_get_contents($file);

        if (is_bool($contents)) {
            throw new FilesystemException(sprintf('error reading file "%s"', $file));
        }

        return $contents;
    }

    public static function create(string $file): void
    {
        $bytes = file_put_contents($file, '');

        if (is_bool($bytes)) {
            throw new FilesystemException(sprintf('error creating file "%s"', $file));
        }
    }

    public static function write(string $file, string $contents): void
    {
        $bytes = file_put_contents($file, $contents);

        if (is_bool($bytes)) {
            throw new FilesystemException(sprintf('error writing to file "%s"', $file));
        }
    }

    public static function append(string $file, string $contents): void
    {
        $resource = fopen($file, 'a+');

        if (!$resource) {
            throw new FilesystemException(sprintf('error appending to file "%s"', $file));
        }

        fwrite($resource, $contents);

        fclose($resource);
    }

    public static function delete(string $file): void
    {
        $success = unlink($file);

        if (!$success) {
            throw new FilesystemException(sprintf('error deleting file "%s"', $file));
        }
    }

    public static function name(string $file, bool $includeExtension = false): string
    {
        $basename = basename($file);

        if ($includeExtension) {
            return $basename;
        }

        if (str_starts_with($basename, '.')) {
            return $basename;
        }

        $dotCount = substr_count($basename, '.');
        $parts = explode('.', $basename);

        if ($dotCount == 1) {
            return $parts[0];
        }

        return implode(
            '.',
            array_slice(
                $parts,
                0,
                (count($parts) - 1)
            )
        );
    }

    public static function mimeType(string $file): string
    {
        $mimeType = mime_content_type($file);

        if (is_bool($mimeType)) {
            throw new FilesystemException(sprintf('mimetype not set for file "%s"', $file));
        }

        return $mimeType;
    }
}
