<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Pathfinders;

use Illuminate\Support\Str;

class Pathfinder
{
    protected string $fqn;
    protected string $controllerPath;
    protected string $suffix;
    protected array $segments = [];

    /**
     * @param string $fqn fully qualified name of the controller
     * @param string|null $controllerPath the path to the controller (default: 'Http\\Controllers')
     * @param string|null $suffix the suffix of the controller (default: 'Controller')
     */
    public function __construct(string $fqn, ?string $controllerPath = 'Http\\Controllers', ?string $suffix = 'Controller')
    {
        $this->fqn = $fqn;
        $this->controllerPath = $controllerPath;
        $this->suffix = $suffix;
    }

    public static function create(string $fqn, ?string $controllerPath = 'Http\\Controllers', ?string $suffix = 'Controller')
    {
        return new static($fqn, $controllerPath, $suffix);
    }

    protected function segments(): array
    {
        if ($this->segments) {
            return $this->segments;
        }

        $fqn = $this->fqn;

        // Remover App\
        $appFolder = $this->getAppFolder();
        if (Str::startsWith($fqn, $appFolder.'\\')) {
            $fqn = Str::after($fqn, $appFolder.'\\');
        }

        // Remover Http\Controllers\
        if (Str::startsWith($fqn, $this->controllerPath.'\\')) {
            $fqn = Str::after($fqn, $this->controllerPath.'\\');
        }

        $segments = explode('\\', $fqn);

        if (count($segments) < 3) {
            throw new \RuntimeException(
                "Invalid controller path [{$this->fqn}]. Expected structure: {$this->getAppFolder()}\\{$this->controllerPath}\\{Environment}\\{Context}\\{Tool}{$this->suffix}"
            );
        }
        $this->segments = $segments;

        return $this->segments;
    }

    public function getFqn(): string
    {
        return $this->fqn;
    }

    public function getAppFolder(): string
    {
        return Str::before($this->fqn, '\\');
    }

    public function getControllerPath(): string
    {
        return $this->controllerPath;
    }

    public function getSuffix(): string
    {
        return $this->suffix;
    }

    public function getEnvironment(): string
    {
        $segments = $this->segments();

        return $segments[0];
    }

    public function getContext(): string
    {
        $segments = $this->segments();

        return $segments[1];
    }

    public function getResource(): string
    {
        $segments = $this->segments();
        $resource = $segments[2];

        if (!Str::endsWith($resource, $this->suffix)) {
            throw new \RuntimeException(
                "Controller [{$this->fqn}] must end with suffix {$this->suffix}"
            );
        }

        return Str::beforeLast($resource, $this->suffix);
    }
}
