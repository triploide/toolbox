<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Managers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property string $policy
 * @method string policy()
 * @property bool $useAuthorize
 * @method bool useAuthorize()
 * 
 */

trait AuthorizationManager
{
    /**
     * @param ?Model
     * @return self
     * 
     * @throws AuthorizationException
     */
    protected function authorize(?Model $model = null, ?string $ability = null): self
    {
        $model ??= $this->getModel();

        if ($this->mustApply('authorize')) {
            $policyClass = $this->resolveClass('policy');
            
            $policy = new $policyClass;

            $ability ??= $this->action;

            $response = $policy->$ability(request()->user(), $model);

            if ($response === false) {
                throw new AuthorizationException("Unauthorized to perform the action: $ability");
            }
        }

        return $this;
    }

    public function useAuthorize(): bool
    {
        return class_exists($this->resolveClass('policy'));
    }
}
