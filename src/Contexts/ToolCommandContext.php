<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Contexts;

class ToolCommandContext
{
    public function __construct(
        public ?string $resource = null,
        public ?array $enviroments = [],
        public ?array $contexts = [],
        public ?array $tools = [],
    ) {
    }
}