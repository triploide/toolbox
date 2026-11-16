<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Resolvers;

use Illuminate\Support\Str;
use Triploide\Toolbox\Pathfinders\Pathfinder;

class ActionResolver extends Resolver
{
    public function __construct(private Pathfinder $pathfinder)
    {
    }

    public function candidates(string $tool): array
    {
        $action = Str::studly($tool);

        $appFolder = $this->pathfinder->getAppFolder();
        $environment = $this->pathfinder->getEnvironment();
        $context = $this->pathfinder->getContext();
        $resource = $this->pathfinder->getResource();

        $candidates = [
            "{$appFolder}\\Actions\\{$environment}\\{$context}\\{$resource}\\{$action}{$resource}", // e.g. App\Actions\Api\Admin\Order\StoreOrder
            "{$appFolder}\\Actions\\Core\\{$action}{$resource}", // e.g. App\Actions\Core\StoreOrder
        ];

        return $candidates;
    }

    protected function defaultCandidate(string $tool): string
    {
        return array_last($this->candidates($tool));
    }
}
