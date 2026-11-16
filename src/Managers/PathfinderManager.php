<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Managers;

use Triploide\Toolbox\Pathfinders\Pathfinder;

/**
 *
 * @property string $pathfinder - Fully qualified name of the pathfinder class (e.g. App\Pathfinders\PostPathfinder)
 * @method string pathfinder()
 * 
 */

trait PathfinderManager
{
    /**
     * @return Pathfinder
     */
    protected function getPathfinder(): Pathfinder
    {
        return Pathfinder::create($this::class);
    }
}
