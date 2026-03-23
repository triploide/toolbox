<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Managers;

use Triploide\Toolbox\Dataproviders\CrudDataprovider as Dataprovider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 *
 * @property string $dataprovider
 * @method string dataprovider()
 * 
 */

trait DataproviderManager
{
    private Model|Collection|LengthAwarePaginator|null $retrievedData = null;
    public $pagination = null;

    /**
     * @return Model|Collection|LengthAwarePaginator|null
     */
    public function retrievedData(): Model|Collection|LengthAwarePaginator|null
    {
        return $this->retrievedData;
    }

    /**
     * @param Dataprovider $dataprovider
     * @return Collection|LengthAwarePaginator
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    protected function pagination(Dataprovider $dataprovider): Collection|LengthAwarePaginator
    {
        $allowedTypes = ['paginate', 'simplePaginate', 'cursorPaginate' ];

        if (request()->has('pagination')) {
            $type = request()->input('pagination.type', 'paginate');
            $type = in_array($type, $allowedTypes) ? $type : 'paginate';

            $response = $this->$type($dataprovider);
        } else {
            $response =$dataprovider->get();
        }

        return $response;
    }

    /**
     * Retrieve the data from the dataprovider.
     * 
     * @return Model|Collection|LengthAwarePaginator|null
     */
    protected function retrieve(): Model|Collection|LengthAwarePaginator|null
    {
        if ($dataprovider = $this->resolveDataprovider()) {
            if (!method_exists($dataprovider, $this->action)) {
                return null;
            }

            $action = $this->action;

            $dataprovider->$action($this->parameters['id'] ?? null);

            $retriveMethod = $this->needToReriveModel() ? 'fetchOne' : 'fetchAll';

            $this->retrievedData = $dataprovider->$retriveMethod();

            // If the data provider retrieves a Model (for example in the show, update or delete methods) we set the model instance to the retrieved data, so we can use it in the repository to mutate it.
            if ($this->retrievedData instanceof Model) {
                $this->modelInstance = $this->retrievedData;
            }
        }

        return $this->retrievedData;
    }

    public function needToReriveModel(): bool
    {
        $modelPlaceholder = strtolower(class_basename($this->getModel())); // eg. For Beat model it will be 'beat'

        return isset($this->parameters[$modelPlaceholder]) || isset($this->parameters['id']);
    }

    /**
     * @return Dataprovider
     */
    protected function resolveDataprovider(): Dataprovider
    {
        return $this->resolveClass('dataprovider', function ($class) {
            $dataprovider = class_exists($class) ? $class : Dataprovider::class;

            return new $dataprovider(request()->all());
        });
    }
}
