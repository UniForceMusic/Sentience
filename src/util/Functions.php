<?php

use src\app\Stdio;

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

function getPublicDir(): string
{
    return appendToBaseDir(BASEDIR, PUBLICDIR);
}

function getPagesDir(): string
{
    return appendToBaseDir(BASEDIR, PAGESDIR);
}

function getComponentsDir(): string
{
    return appendToBaseDir(BASEDIR, COMPONENTSDIR);
}

function getTestsDir(): string
{
    return appendToBaseDir(BASEDIR, TESTSDIR);
}

function appendToBaseUrl(string $baseUrl, ?string $path, string $glue = '/'): ?string
{
    if (!$path) {
        return null;
    }

    return sprintf(
        '%s%s%s',
        rtrim($baseUrl, $glue),
        $glue,
        ltrim($path, $glue),
    );
}

function appendToBaseDir(string $baseDir, ?string ...$paths): ?string
{
    if (!$paths) {
        return null;
    }

    $chars = ['/', '\\'];

    foreach ($chars as $char) {
        $baseDir = rtrim($baseDir, $char);

        foreach ($paths as $index => $path) {
            $paths[$index] = trim($path, $char);
        }
    }

    return implode(
        DIRECTORY_SEPARATOR,
        [
            $baseDir,
            ...$paths
        ]
    );
}

function component(string $name, array $vars = []): void
{
    $componentsDir = getComponentsDir();
    $lcName = strtolower($name);

    foreach ($vars as $var => $value) {
        $$var = $value;
    }

    if (!str_ends_with($lcName, '.php')) {
        $lcName = sprintf('%s.php', $lcName);
    }

    $componentPath = appendToBaseDir(
        $componentsDir,
        $lcName
    );

    if (!file_exists($componentPath)) {
        Stdio::errorFLn('component "%s" does not exist', $componentPath);
        return;
    }

    include $componentPath;
}
