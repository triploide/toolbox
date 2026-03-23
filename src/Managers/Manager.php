<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Managers;

use Closure;
use Triploide\Toolbox\Resolvers\DefaultResolver;

trait Manager
{
    /**
     * Determine if a tool must be applied or not.
     * 
     * The determination is based on the following order:
     * 1. A method with the name use{Tool}() (e.g. useDataprovider())
     * 2. A property with the name $use{Tool} (e.g. $useDataprovider)
     * 3. The default value passed as a parameter (e.g. true)
     * 
     * @param string $tool
     * @param bool $default
     * @return bool
     */
    private function mustApply(string $tool, bool $default = true): bool
    {
        $must_apply = $default;

        $key = 'use' . ucfirst($tool);

        if (method_exists($this, $key)) {
            $must_apply = $this->$key();
        } else if (property_exists($this, $key)) {
            $must_apply = $this->$key;
        }

        return $must_apply;
    }

    /**
     * Resolve a class name from the controller properties or methods.
     * The resolution order is the following:
     * 1. A method with the same name as the tool (e.g. dataprovider())
     * 2. A property with the same name as the tool (e.g. $dataprovider)
     * 3. A method with the name get{Tool}FullyQualifiedName() (e.g. getDataproviderFullyQualifiedName())
     * 4. The default fully qualified name (e.g. App\Dataproviders\Domain\Profile\ModelDataprovider)
     * 
     * @param string $tool
     * @param Closure|null $callback
     * @return mixed
     */
    private function resolveClass($tool, ?Closure $callback = null): mixed
    {
        $class =
            $this->resolveFromMethod($tool)
            ?? $this->resolveFromProperty($tool)
            ?? $this->resolveFromCustomResolver($tool)
            ?? $this->resolveFromConvention($tool)
        ;

        return (is_null($callback)) ? $class : $callback($class);
    }

    private function resolveFromMethod(string $tool): ?string
    {
        if (method_exists($this, $tool)) {
            return $this->$tool();
        }

        return null;
    }

    private function resolveFromProperty(string $tool): ?string
    {
        if (property_exists($this, $tool)) {
            return $this->$tool;
        }

        return null;
    }

    private function resolveFromCustomResolver(string $tool): ?string
    {
        $method = 'get' . ucfirst($tool) . 'FullyQualifiedName';

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return null;
    }

    private function resolveFromConvention(string $tool): string
    {
        $resolver = new DefaultResolver($this->getPathFinder()); // TODO: Reduce coupling

        return $resolver->getFullyQualifiedName($tool);
    }
}
