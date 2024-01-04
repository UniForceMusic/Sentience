<?php

function isCli(): bool
{
    return (empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0);
}

function replaceNonAsciiChars(string $string): string
{
    return preg_replace('/[[:^print:]]/', '', $string);
}

function getMigrationsDir(): string
{
    return appendToBaseDir(BASEDIR, MIGRATIONSDIR);
}

function getFileDir(): string
{
    return appendToBaseDir(BASEDIR, FILEDIR);
}

function getTestsDir(): string
{
    return appendToBaseDir(BASEDIR, TESTSDIR);
}

function appendToBaseUrl(string $baseUrl, string $path, string $glue = '/'): ?string
{
    if (!$path) {
        return null;
    }

    return sprintf(
        '%s%s%s',
        trim($baseUrl, $glue),
        $glue,
        trim($path, $glue),
    );
}

function appendToBaseDir(string $baseDir, string $relativePath): string
{
    $chars = ['/', '\\'];

    foreach ($chars as $char) {
        $baseDir = rtrim($baseDir, $char);
        $relativePath = trim($relativePath, $char);
    }

    return sprintf(
        '%s%s%s',
        $baseDir,
        DIRECTORY_SEPARATOR,
        $relativePath
    );
}
