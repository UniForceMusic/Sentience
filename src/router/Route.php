<?php

namespace src\router;

class Route
{
    protected string $path;
    protected array $callable;
    protected array $methods;
    protected array $templateValues;

    public function __construct(string $path, array $callable, array $methods)
    {
        $this->path = $path;
        $this->callable = $callable;
        $this->methods = $this->methodsToLowercase($methods);
        $this->templateValues = [];
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
            if (preg_match('/{(.*)}/', $part, $matches)) {
                if (count($matches) >= 1 && key_exists($index, $requestUriParts)) {
                    $this->templateValues[$matches[1]] = $requestUriParts[$index];
                }

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

    public function getPath(): string
    {
        return $this->path;
    }

    public function getCallable(): array
    {
        return $this->callable;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getTemplateValues(): array
    {
        return $this->templateValues;
    }
}

?>