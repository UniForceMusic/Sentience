<?php

namespace src\app;

class Stdio
{
    public static function print(string $input)
    {
        file_put_contents('php://stdout', $input);
    }

    public static function printLn(string $input)
    {
        static::print($input . PHP_EOL);
    }

    public static function printF(string $input, ...$values)
    {
        static::print(sprintf($input, ...$values));
    }

    public static function printFLn(string $input, ...$values)
    {
        static::printF($input . PHP_EOL, ...$values);
    }

    public static function error(string $input)
    {
        file_put_contents('php://stderr', $input);
    }

    public static function errorLn(string $input)
    {
        static::error($input . PHP_EOL);
    }

    public static function errorF(string $input, ...$values)
    {
        static::error(sprintf($input, ...$values));
    }

    public static function errorFLn(string $input, ...$values)
    {
        static::errorF($input . PHP_EOL, ...$values);
    }

    public static function commandNotFound(array $commands)
    {
        static::errorLn('command not found. these are the available commands:');

        foreach ($commands as $command) {
            static::errorFLn('- %s', $command->getCommand());
        }
    }
}
