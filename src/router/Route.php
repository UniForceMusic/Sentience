<?php

namespace src\router;

use Closure;

class Route
{
    protected string $path;
    protected string|array|Closure $callable;
    protected array $methods;
    protected array $middleware;
    protected array $templateValues;
    protected bool $isFile;
    protected ?string $request;

    public function __construct()
    {
        $this->path = '';
        $this->methods = [];
        $this->middleware = [];
        $this->templateValues = [];
        $this->isFile = false;
        $this->request = null;
    }

    public static function create(): static
    {
        return new static();
    }

    public function isMatch(string $requestUri, string $method): bool
    {
        $pathMatch = ($this->isFile) ? $this->isFileMatch($requestUri) : $this->isPathMatch($requestUri);
        $methodMatch = $this->isMethodMatch($method);

        return ($pathMatch && $methodMatch);
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

            $meetsKeyConditions = (count($matches) > 1);
            if (!$meetsKeyConditions) {
                continue;
            }

            if (key_exists($index, $requestUriParts)) {
                $templateKey = $matches[1];
                $templateValue = $requestUriParts[$index];
                $this->templateValues[$templateKey] = $templateValue;
            }

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

    protected function isFileMatch(string $requestUri): bool
    {
        $pattern = sprintf('/%s\/(.*)/', FILEDIR);
        $matchesTemplateSyntax = preg_match($pattern, $requestUri, $matches);
        if (!$matchesTemplateSyntax) {
            return false;
        }

        if (count($matches) > 1) {
            $this->templateValues['filePath'] = $matches[1];
        }

        return true;
    }

    protected function isMethodMatch(string $method): bool
    {
        if (in_array('*', $this->methods)) {
            return true;
        }

        return in_array(
            strtoupper($method),
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

    public function getRequest(): ?string
    {
        return $this->request;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;
        return $this;
    }

    public function setCallable(array|string|callable $callable): static
    {
        $this->callable = $callable;
        return $this;
    }

    public function setMethods(array $methods): static
    {
        $this->methods = $this->methodsToUppercase($methods);
        return $this;
    }

    public function setMiddleware(array $middleware): static
    {
        $this->middleware = $middleware;
        return $this;
    }

    public function setFile(bool $state): static
    {
        $this->isFile = $state;
        return $this;
    }

    public function setRequest(string $request): static
    {
        $this->request = $request;
        return $this;
    }

    protected function methodsToUppercase(array $methods): array
    {
        return array_map(
            function (string $method): string {
                return strtoupper($method);
            },
            $methods
        );
    }
}
