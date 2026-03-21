<?php

declare(strict_types=1);

namespace Toolbox\Resolvers;

use Toolbox\Pathfinders\Pathfinder;

abstract class Resolver
{
    public function __construct(private Pathfinder $pathfinder)
    {
    }

    public function getFullyQualifiedName(string $tool): string
    {
        foreach ($this->candidates($tool) as $candidate) {
            if (class_exists($candidate)) {
                return $candidate;
            }
        }

        return $this->defaultCandidate($tool);
    }

    abstract public function candidates(string $tool): array;

    protected function defaultCandidate(string $tool): string
    {
        return array_last($this->candidates($tool));
    }
}