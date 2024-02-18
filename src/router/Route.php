<?php

namespace src\router;

use Closure;

class Route
{
    protected string $path;
    protected null|string|array|Closure $callable;
    protected array $methods;
    protected array $middleware;
    protected array $vars;
    protected bool $hide;
    protected ?string $request;

    public static function create(): static
    {
        return new static();
    }

    public function __construct()
    {
        $this->path = '';
        $this->callable = null;
        $this->methods = [];
        $this->middleware = [];
        $this->vars = [];
        $this->hide = false;
        $this->request = null;
    }

    public function isMatch(string $requestUri, string $method): bool
    {
        $pathMatch = $this->isPathMatch($requestUri);
        $methodMatch = $this->isMethodMatch($method);

        return ($pathMatch && $methodMatch);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getCallable(): null|array|string|callable
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

    public function getVars(): array
    {
        return $this->vars;
    }

    public function getHide(): bool
    {
        return $this->hide;
    }

    public function getRequest(): ?string
    {
        return $this->request;
    }

    public function setPath(string $path): static
    {
        $path = trim($path, '/');
        $path = sprintf('/%s', $path);

        $this->path = $path;

        return $this;
    }

    public function setCallable(array|string|callable $callable): static
    {
        $this->callable = $callable;

        return $this;
    }

    public function setMethods(string|array $methods): static
    {
        if (is_string($methods)) {
            $methods = [$methods];
        }

        $this->methods = $this->methodsToUppercase($methods);

        return $this;
    }

    public function setMiddleware(array $middleware): static
    {
        $this->middleware = $middleware;

        return $this;
    }

    public function setVar(string $key, string $value): static
    {
        $this->vars[$key] = $value;

        return $this;
    }

    public function setVars(array $values): static
    {
        foreach ($values as $key => $value) {
            $this->vars[$key] = $value;
        }

        return $this;
    }

    public function setHide(): static
    {
        $this->hide = true;

        return $this;
    }

    public function setRequest(string $request): static
    {
        $this->request = $request;

        return $this;
    }

    protected function isPathMatch(string $requestUri): bool
    {
        $routeParts = explode('/', trim($this->path, '/'));
        $requestUriParts = explode('/', trim($requestUri, '/'));
        $modifiedParts = [];

        foreach ($routeParts as $index => $part) {
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
                $key = $matches[1];
                $value = $requestUriParts[$index];
                $this->vars[$key] = $value;
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

    protected function isMethodMatch(string $method): bool
    {
        if (count($this->methods) < 1) {
            return true;
        }

        if (in_array('*', $this->methods)) {
            return true;
        }

        return in_array(
            strtoupper($method),
            $this->methods
        );
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
