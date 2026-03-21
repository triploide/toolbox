<?php

declare(strict_types=1);

namespace Toolbox\Resolvers;

use Toolbox\Pathfinders\Pathfinder;

class ModelResolver extends Resolver
{
    public function __construct(private Pathfinder $pathfinder)
    {
    }

    public function candidates(string $tool): array
    {
        $appFolder = $this->pathfinder->getAppFolder();
        $resource = $this->pathfinder->getResource();
        $modelPath = 'Models';

        $candidates = [
            "{$appFolder}\\{$modelPath}\\$resource"
        ];

        return $candidates;
    }

    protected function defaultCandidate(string $tool): string
    {
        return array_last($this->candidates($tool));
    }
}