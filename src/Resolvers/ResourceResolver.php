<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Resolvers;

use Illuminate\Support\Str;
use Triploide\Toolbox\Pathfinders\Pathfinder;

class ResourceResolver extends Resolver
{
    public function __construct(private Pathfinder $pathfinder)
    {
    }

    public function candidates(string $tool): array
    {
        $classname = Str::studly($tool);
        $folder = Str::plural($classname);

        $appFolder = $this->pathfinder->getAppFolder();
        $environment = $this->pathfinder->getEnvironment();
        $context = $this->pathfinder->getContext();
        $resource = $this->pathfinder->getResource();

        $candidates = [
            "{$appFolder}\\Http\\{$folder}\\{$environment}\\{$context}\\{$resource}{$classname}", // e.g. App\Http\Resources\Api\Admin\PostResource
            "{$appFolder}\\Http\\{$folder}\\Core\\{$resource}{$classname}", // e.g. App\Http\Resources\Core\PostResource
        ];

        return $candidates;
    }

    protected function defaultCandidate(string $tool): string
    {
        return array_last($this->candidates($tool));
    }
}