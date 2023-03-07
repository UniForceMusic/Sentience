<?php

namespace src\router;

class Route
{
    protected string $path;
    protected array $callable;
    protected array $methods;

    public function __construct(string $path, array $callable, array $methods)
    {
        $this->path = $path;
        $this->callable = $callable;
        $this->methods = $this->methodsToLowercase($methods);
    }

    public static function create(string $path, array $function, array $methods): static
    {
        return new static(
            $path,
            $function,
            $methods
        );
    }

    protected function methodsToLowercase(array $methods): array
    {
        return array_map(
            function ($method) {
                return strtolower($method);
            },
            $methods
        );
    }

    public function isMatch(string $requestUri, string $method): bool
    {
        return ($this->isPathMatch($requestUri) && $this->isMethodMatch($method));
    }

    protected function isPathMatch(string $requestUri): bool
    {
        $templateParts = explode('/', trim($this->path, '/'));
        $modifiedParts = [];

        foreach ($templateParts as $index => $part) {
            if (preg_match('/{(.*)}/', $part, $matches)) {
                $modifiedParts[] = '(.[^/]*)';
            } else {
                $modifiedParts[] = $part;
            }
        }

        $joinedParts = trim(
            implode(
                '/',
                $modifiedParts
            ),
            '/'
        );

        $regex = sprintf(
            '/^%s$/',
            str_replace('/', '\/', $joinedParts)
        );

        return preg_match($regex, $requestUri);
    }

    protected function isMethodMatch(string $method): bool
    {
        return in_array(
            strtolower($method),
            $this->methods
        );
    }

    public function getCallable(): callable
    {
        $className = sprintf('src\controllers\%s', $this->callable[0]);
        $methodName = $this->callable[1];

        $class = new $className();

        return [$class, $methodName];
    }
}

?>