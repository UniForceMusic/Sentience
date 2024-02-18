<?php

namespace src\router;

use Closure;

class Command
{
    protected string $argument;
    protected array|string|Closure $callable;
    protected array $middleware;
    protected array $args;
    protected array $flags;
    protected array $words;
    protected bool $argsParsed;

    public static function create(): static
    {
        return new static();
    }

    public function __construct()
    {
        $this->argument = '';
        $this->middleware = [];
        $this->args = [];
        $this->flags = [];
        $this->words = [];
        $this->argsParsed = false;
    }

    public function injectArgs(array $args): static
    {
        $this->args = $args;

        return $this;
    }

    public function isMatch(string $argument): bool
    {
        return ($argument == $this->argument);
    }

    public function getArgument(): string
    {
        return $this->argument;
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
        $this->parseArgs();

        return $this->flags;
    }

    public function getWords(): array
    {
        $this->parseArgs();

        return $this->words;
    }

    public function setArgument(string $argument): static
    {
        $this->argument = $argument;

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

    protected function parseArgs(): void
    {
        if ($this->argsParsed) {
            return;
        }

        $usableArgs = array_slice($this->args, 2);

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
