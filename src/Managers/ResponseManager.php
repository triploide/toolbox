<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Managers;

use Illuminate\Database\Eloquent\Model;
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
        $resource = $this->resolveClass('resource');

        $response = ($this->retrievedData instanceof Model) ? new $resource($this->retrievedData) : $resource::collection($this->retrievedData ?? []);

        return $response->additional($this->extraResponse($response));
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