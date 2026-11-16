<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Managers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Triploide\Toolbox\Resolvers\ResourceResolver;

/**
 *
 * @property string $resource
 * @method string resource()
 * 
 */

trait ResponseManager
{
    /**
     * Create the response for the request
     * 
     * @return \Illuminate\Http\Resources\Json\JsonResource|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function reply() : \Illuminate\Http\Resources\Json\JsonResource|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $resource = $this->resolveClass('resource');

        if ($this->retrievedData instanceof Model) {
            $response =  new $resource($this->retrievedData);
        } else if (
            $this->retrievedData instanceof Collection
            || $this->retrievedData instanceof Paginator
            || $this->retrievedData instanceof CursorPaginator
            || $this->retrievedData instanceof LengthAwarePaginator
        ) {
            $response = $resource::collection($this->retrievedData->items());
        }

        return $response;
    }
    
    /**
     * Get the fully qualified name of the resource class.
     * 
     * @return string
     */
    private function getResourceFullyQualifiedName(): string
    {
        $resolver = new ResourceResolver($this->getPathFinder()); // TODO: Reduce coupling

        return $resolver->getFullyQualifiedName($this->action);
    }
}
