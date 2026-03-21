<?php

declare(strict_types=1);

namespace Toolbox\Http\Controllers\Traits;

trait BootstrapTrait
{
    public string $action;
    public ?array $parameters;

    /**
     * @param  string $action
     * @return void
     */
    private function boot(string $action): void
    {
        $this->action = $action;

        $this->parameters = request()->route()->parameters;
    }
}