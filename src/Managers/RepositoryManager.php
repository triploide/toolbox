<?php

declare(strict_types=1);

namespace Toolbox\Managers;

use Illuminate\Database\Eloquent\Model;
use Toolbox\Repositories\Repository;

/**
 *
 * @property string $repository
 * @method string repository()
 * 
 */

trait RepositoryManager
{
    private function mutate(): void
    {
        if ($repository = $this->getRepository()) {
            if (!method_exists($repository, $this->action)) {
                return;
            }

            $action = $this->action;

            $response = $repository->$action();

            if ($response instanceof Model) {
                $this->modelInstance = $response;
            }

            $this->retrievedData = $response;
        }
    }

    protected function getRepository(): ?Repository
    {
        return $this->resolveClass('repository', function ($class) {
            if (!class_exists($class)) {
                return null;
            }

            $repository = new $class(request()->all());

            $repository->setModel($this->getModel());

            return $repository;
        });
    }
}