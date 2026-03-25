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
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function reply()
    {
        // TODO: refactor

        $resource = $this->resolveClass('resource');

        if ($this->retrievedData instanceof Model) {
            $response =  new $resource($this->retrievedData);
            $response->setMethod($this->action);
        } elseif ($this->retrievedData instanceof Collection) {
            $response = $resource::collection($this->retrievedData ?? []);
            $response->each->setMethod($this->action);
        } else if ($this->retrievedData instanceof Paginator) {
            $response = $resource::collection($this->retrievedData->items());
            $paginationData = $this->retrievedData->toArray();
            $response->each->setMethod($this->action);
            $response->additional($paginationData);
            $response->additional([
                'current_page' => $paginationData['current_page'],
                'from' => $paginationData['from'],
                'per_page' => $paginationData['per_page'],
                'to' => $paginationData['to'],
            ]);
        } else if ($this->retrievedData instanceof CursorPaginator) {
            $response = $resource::collection($this->retrievedData->items());
            $paginationData = $this->retrievedData->toArray();
            $response->each->setMethod($this->action);
            $response->additional([
                'per_page' => $paginationData['per_page'],
                'next_cursor' => $paginationData['next_cursor'],
                'prev_cursor' => $paginationData['prev_cursor'],
            ]);
        } else if ($this->retrievedData instanceof LengthAwarePaginator) {
            $response = $resource::collection($this->retrievedData->items());
            $paginationData = $this->retrievedData->toArray();
            $response->each->setMethod($this->action);
            $response->additional([
                'current_page' => $paginationData['current_page'],
                'from' => $paginationData['from'],
                'last_page' => $paginationData['last_page'],
                'per_page' => $paginationData['current_page'],
                'to' => $paginationData['to'],
                'total' => $paginationData['total'],
            ]);
        }

        $additional = [
            ...$response->additional,
            ...$this->extraResponse($response)
        ];

        return $response->additional($additional);
    }

    /**
     * Override this method in your controller to add extra values to the response
     * 
     * @param mixed $response
     * @return array
     */
    protected function extraResponse($response): array
    {
        return [];
    }

    /**
     * Get the fully qualified name of the resource class.
     * 
     * @return string
     */
    private function getResourceFullyQualifiedName(): string
    {
        $resolver = new ResourceResolver($this->getPathFinder()); // TODO: Reduce coupling

        return $resolver->getFullyQualifiedName('resource');
    }
}
