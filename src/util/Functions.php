<?php

function isCli(): bool
{
    return (empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0);
}

function stripNonAscii(string $string): string
{
    return preg_replace('/[[:^print:]]/', '', $string);
}

?>