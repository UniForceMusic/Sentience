<?php

namespace src\router;

class CliRouter
{
    protected string $argument;
    protected array $commands;

    public function __construct(array $args, array $commands)
    {
        /**
         * Argsv array:
         * [0] filename
         * [1] command name
         * [2...] flags and commands
         */
        $this->argument = $args[1] ?? '';
        $this->commands = [];

        foreach ($commands as $command) {
            $this->commands[] = $command->injectArgs($args);
        }
    }

    public function getMatch(): ?Command
    {
        foreach ($this->commands as $command) {
            if ($command->isMatch($this->argument)) {
                return $command;
            }
        }

        return null;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }
}
