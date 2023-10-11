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
    return sprintf(
        '%s%s%s',
        BASEDIR,
        DIRECTORY_SEPARATOR,
        MIGRATIONSDIR
    );
}

function getFileDir(): string
{
    return sprintf(
        '%s%s%s',
        BASEDIR,
        DIRECTORY_SEPARATOR,
        FILEDIR
    );
}
