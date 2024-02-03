<?php

namespace src\app;

use Throwable;
use Service;
use src\router\CliRouter;
use src\router\Command;
use src\util\Strings;

class CliApp extends App implements AppInterface
{
    protected CliRouter $router;

    public function __construct(array $commands, Service $service, array $args)
    {
        $this->router = new CliRouter($args, $commands);
        $this->service = $service;
    }

    public function execute(): void
    {
        try {
            $command = $this->router->getMatch();
            if (!$command) {
                Stdio::commandNotFound($this->router->getCommands());
                return;
            }

            $words = $command->getWords();
            $flags = $command->getFlags();

            $args = $this->getArgs($command, $this->service, $words, $flags);
            if (!is_array($args)) {
                Stdio::errorLn('error getting arguments for callable');
                return;
            }

            $modifiedArgs = $this->executeMiddleware(
                $command,
                $args,
                $this->service,
                $words,
                $flags
            );
            if (!is_array($modifiedArgs)) {
                return;
            }

            $callable = $command->getCallable();
            if (is_array($callable)) {
                $callable = $this->arrayToCallable(
                    $callable,
                    function (string $class, string $method): void {
                        Stdio::errorFLn('class %s does not have a public method named: %s', $class, $method);
                    }
                );

                if (!$callable) {
                    return;
                }
            }

            $callable(...$args);
        } catch (Throwable $error) {
            $this->handleException($error);
        }
    }

    public function handleException(Throwable $error): void
    {
        Stdio::errorLn('--------- Exception -------------------------------------------------------');
        Stdio::errorFLn('- Text  : %s', $error->getMessage());
        Stdio::errorFLn('- Type  : %s', $error::class);
        Stdio::errorFLn('- File  : %s', Strings::strip(BASEDIR . DIRECTORY_SEPARATOR, $error->getFile()));
        Stdio::errorFLn('- Line  : %s', $error->getLine());

        $subtractFromIndex = 0;

        if (($_ENV['APP_STACK_TRACE'] ?? false)) {
            Stdio::errorLn('- Trace :');
            foreach ($error->getTrace() as $index => $trace) {
                if (!isset($trace['file'])) {
                    $subtractFromIndex++;
                    continue;
                }

                Stdio::error('      ');

                if (isset($trace['class'])) {
                    Stdio::errorFLn(
                        '%s : %s:%s %s%s%s()',
                        ($index + 1 - $subtractFromIndex),
                        Strings::strip(BASEDIR . DIRECTORY_SEPARATOR, $trace['file']),
                        $trace['line'],
                        $trace['class'],
                        $trace['type'],
                        $trace['function']
                    );
                    continue;
                }

                Stdio::errorFLn(
                    '%s : %s:%s %s()',
                    ($index + 1 - $subtractFromIndex),
                    Strings::strip(BASEDIR . DIRECTORY_SEPARATOR, $trace['file']),
                    $trace['line'],
                    $trace['function']
                );
            }
        }

        Stdio::errorLn('---------------------------------------------------------------------------');
    }

    protected function getArgs(Command $command, Service $service, array $words, array $flags): ?array
    {
        $callable = $command->getCallable();
        if (!$callable) {
            return null;
        }

        if (is_array($callable)) {
            $arguments = $this->getMethodArgs($callable[0], $callable[1]);
        } else {
            $arguments = $this->getFunctionArgs($callable);
        }

        $serviceMethods = get_class_methods($service);

        $args = [];
        foreach ($arguments as $argument) {
            $name = $argument->getName();

            if ($name == 'words') {
                $args['words'] = $words;
                continue;
            }

            if ($name == 'flags') {
                $args['flags'] = $flags;
                continue;
            }

            if (!in_array($name, ['words', 'flags', ...$serviceMethods])) {
                $args[$name] = null;
                continue;
            }

            $callable = [$service, $name];
            $args[$name] = $callable();
        }

        return $args;
    }

    protected function executeMiddleware(Command $command, array $args, Service $service, array $words, array $flags): ?array
    {
        $middleware = $command->getMiddleware();

        $modifiedArgs = $args;

        foreach ($middleware as $callable) {
            $modifiedArgs = $this->executeMiddlewareCallable(
                $callable,
                $modifiedArgs,
                $service,
                null,
                $words,
                $flags
            );

            if (is_null($modifiedArgs)) {
                return null;
            }
        }

        return $modifiedArgs;
    }
}
