<?php

namespace Triploide\Toolbox\Traits;

use Triploide\Toolbox\EloquentJoinBuilder;

trait JoinRelationTrait
{
    /**
     * Handle dynamic method calls to the relationship.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if ($this->getQuery() instanceof EloquentJoinBuilder) {
            $this->getQuery()->relationClauses[] = [$method => $parameters];
        }

        return parent::__call($method, $parameters);
    }
}
