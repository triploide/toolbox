<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Resolvers;

use Illuminate\Support\Str;
use Triploide\Toolbox\Pathfinders\Pathfinder;

class DefaultResolver extends Resolver
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
            "{$appFolder}\\{$folder}\\{$environment}\\{$context}\\{$resource}{$classname}", // e.g. App\Dataproviders\Web\Admin\PostDataprovider
            "{$appFolder}\\{$folder}\\Core\\{$resource}{$classname}", // e.g. App\Dataproviders\Core\PostDataprovider
        ];

        return $candidates;
    }

    protected function defaultCandidate(string $tool): string
    {
        return array_last($this->candidates($tool));
    }
}
