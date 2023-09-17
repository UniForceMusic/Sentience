<?php

namespace src\router;

class CliRouter
{
    protected string $requestedCommand;
    protected array $commands;

    public function __construct(array $args, array $commands)
    {
        /**
         * Argsv array:
         * [0] filename
         * [1] command name
         * [2...] flags and commands
         */
        $requestedCommand = $args[1] ?? '';
        $this->requestedCommand = $requestedCommand;

        foreach ($commands as $index => $command) {
            $command->injectArgs($args);
            $commands[$index] = $command;
        }
        $this->commands = $commands;
    }

    public function getMatch(): ?Command
    {
        foreach ($this->commands as $command) {
            if ($command->isMatch($this->requestedCommand)) {
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
