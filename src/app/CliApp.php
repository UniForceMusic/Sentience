<?php

namespace src\app;

use Throwable;
use Closure;
use ReflectionFunction;
use ReflectionMethod;
use Service;
use src\router\CliRouter;
use src\router\Command;
use src\util\Strings;

class CliApp
{
    protected CliRouter $router;
    protected Service $service;

    public function __construct(array $commands, Service $service, array $args)
    {
        $this->router = new CliRouter($args, $commands);
        $this->service = $service;
    }

    public function execute(): void
    {
        $command = $this->router->getMatch();
        if (!$command) {
            Stdio::commandNotFound($this->router->getCommands());
            return;
        }

        $args = $this->getArgs($command, $this->service);
        if (!is_array($args)) {
            Stdio::errorLn('error getting arguments for callable');
            return;
        }

        $modifiedArgs = $this->executeMiddleware($command, $args);
        if (!is_array($modifiedArgs)) {
            return;
        }

        $callable = $command->getCallable();
        if (is_array($callable)) {
            $callable = $this->arrayToCallable($callable);

            if (!$callable) {
                return;
            }
        }

        try {
            $callable(...$args);
        } catch (Throwable $error) {
            $this->handleException($error);
        }
    }

    protected function arrayToCallable(array $callable): ?callable
    {
        $className = $callable[0];
        $methodName = $callable[1];
        $controller = new $className();

        if (!method_exists($controller, $methodName)) {
            Stdio::errorFLn('class does not have a public method named: "%s"', $methodName);
            return null;
        }

        return [$controller, $methodName];
    }

    protected function handleException(Throwable $error): void
    {
        Stdio::errorLn('--------- Exception -------------------------------------------------------');
        Stdio::errorFLn('- Text  : %s', $error->getMessage());
        Stdio::errorFLn('- Type  : %s', $error::class);
        Stdio::errorFLn('- File  : %s', Strings::strip(BASEDIR . DIRECTORY_SEPARATOR, $error->getFile()));
        Stdio::errorFLn('- Line  : %s', $error->getLine());

        if (($_ENV['STACK_TRACE'] ?? false)) {
            Stdio::errorLn('- Trace :');
            foreach ($error->getTrace() as $index => $trace) {
                Stdio::error('      ');

                if (isset($trace['class'])) {
                    Stdio::errorFLn(
                        '%s : %s:%s %s%s%s()',
                        ($index + 1),
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
                    ($index + 1),
                    Strings::strip(BASEDIR . DIRECTORY_SEPARATOR, $trace['file']),
                    $trace['line'],
                    $trace['function']
                );
            }
        }

        Stdio::errorLn('---------------------------------------------------------------------------');
    }

    protected function getArgs(Command $command, Service $service): ?array
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

            if ($name == 'flags') {
                $args['flags'] = $command->getFlags();
                continue;
            }

            if ($name == 'words') {
                $args['words'] = $command->getWords();
                continue;
            }

            if (!in_array($name, ['flags', 'words', ...$serviceMethods])) {
                $args[$name] = null;
                continue;
            }

            $callable = [$service, $name];
            $args[$name] = $callable();
        }

        return $args;
    }

    protected function getFunctionArgs(string|Closure $callable): array
    {
        $reflectionFunction = new ReflectionFunction($callable);
        return $reflectionFunction->getParameters();
    }

    protected function getMethodArgs(string|object $class, string $method): array
    {
        $reflectionMethod = new ReflectionMethod($class, $method);
        return $reflectionMethod->getParameters();
    }

    protected function executeMiddleware(Command $command, array $args): ?array
    {
        $middleware = $command->getMiddleware();

        $modifiedArgs = $args;

        foreach ($middleware as $middlewareClass) {
            $callable = [(new $middlewareClass()), 'execute'];
            $modifiedArgs = $callable($modifiedArgs);

            if (!$modifiedArgs) {
                return null;
            }
        }

        return $modifiedArgs;
    }
}

?>