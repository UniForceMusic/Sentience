<?php

namespace src\router;

use Closure;

class Command
{
    protected string $command;
    protected array|string|Closure $callable;
    protected array $middleware;
    protected array $args;
    protected ?array $flags;
    protected ?array $words;

    public function __construct()
    {
        $this->command = '';
        $this->middleware = [];
        $this->args = [];
        $this->flags = null;
        $this->words = null;
    }

    public function injectArgs(array $args): static
    {
        $this->args = $args;

        return $this;
    }

    public static function create(): static
    {
        return new static();
    }

    public function isMatch(string $command): bool
    {
        return ($command == $this->command);
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function getCallable(): array|string|callable
    {
        return $this->callable;
    }

    public function getFlags(): array
    {
        if (!$this->flags) {
            $this->parseArgs();
        }

        return $this->flags;
    }

    public function getWords(): array
    {
        if (!$this->words) {
            $this->parseArgs();
        }

        return $this->words;
    }

    public function setCommand(string $command): static
    {
        $this->command = $command;

        return $this;
    }

    public function setMiddleware(array $middleware): static
    {
        $this->middleware = $middleware;

        return $this;
    }

    public function setCallable(array|string|callable $callable): static
    {
        $this->callable = $callable;

        return $this;
    }

    protected function parseArgs()
    {
        $usableArgs = array_slice($this->args, 2);
        $this->flags = [];
        $this->words = [];

        foreach ($usableArgs as $arg) {
            $matchesSyntax = preg_match('/--(.[^=]*)=(.*)/', $arg, $matches);

            if (!$matchesSyntax) {
                $this->words[] = $arg;
                continue;
            }

            $key = $matches[1];
            $value = $matches[2];

            $this->flags[$key] = $value;
        }
    }
}
