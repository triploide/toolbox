<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Resolvers;

use Illuminate\Support\Str;
use Triploide\Toolbox\Pathfinders\Pathfinder;

class RequestResolver extends Resolver
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
            "{$appFolder}\\Http\\Requests\\{$environment}\\{$context}\\{$resource}\\{$action}{$resource}Request", // e.g. App\Http\Requests\Api\Admin\StoreOrderRequest
        ];

        return $candidates;
    }

    protected function defaultCandidate(string $tool): string
    {
        return array_last($this->candidates($tool));
    }
}
