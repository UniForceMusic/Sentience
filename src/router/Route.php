<?php

namespace src\router;

use Closure;

class Route
{
    protected string $path;
    protected array|string|Closure $callable;
    protected array $methods;
    protected array $middleware;
    protected array $templateValues;

    public function __construct(string $path, array|string|callable $callable, array $methods, array $middleware = [])
    {
        $this->path = $path;
        $this->callable = $callable;
        $this->methods = $this->methodsToLowercase($methods);
        $this->middleware = $middleware;
        $this->templateValues = [];
    }

    public static function create(string $path, array|string|callable $function, array $methods, array $middleware = []): static
    {
        return new static(
            $path,
            $function,
            $methods,
            $middleware
        );
    }

    protected function methodsToLowercase(array $methods): array
    {
        return array_map(
            function (string $method): string {
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
        $requestUriParts = explode('/', trim($requestUri, '/'));
        $modifiedParts = [];

        foreach ($templateParts as $index => $part) {
            $matchesTemplateSyntax = preg_match('/{(.*)}/', $part, $matches);
            if (!$matchesTemplateSyntax) {
                $modifiedParts[] = $part;
                continue;
            }

            $meetsKeyConditions = (count($matches) >= 1 && key_exists($index, $requestUriParts));
            if (!$meetsKeyConditions) {
                continue;
            }

            $templateKey = $matches[1];
            $templateValue = $requestUriParts[$index];

            $this->templateValues[$templateKey] = $templateValue;
            $modifiedParts[] = '(.[^/]*)';
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

    public function getPath(): string
    {
        return $this->path;
    }

    public function getCallable(): array|string|callable
    {
        return $this->callable;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function getTemplateValues(): array
    {
        return $this->templateValues;
    }
}

?>