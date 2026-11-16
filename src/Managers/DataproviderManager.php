<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Managers;

use Illuminate\Database\Eloquent\Model;
use Triploide\Toolbox\Dataproviders\CrudDataprovider as Dataprovider;

/**
 *
 * @property string $dataprovider
 * @method string dataprovider()
 * 
 */

trait DataproviderManager
{
    private mixed $retrievedData = null;

    /**
     * @return mixed
     */
    public function retrievedData(): mixed
    {
        return $this->retrievedData;
    }

    /**
     * Retrieve the data from the dataprovider.
     * 
     * @return mixed
     */
    protected function retrieve(): mixed
    {
        if ($dataprovider = $this->resolveDataprovider()) {
            if (!method_exists($dataprovider, $this->action)) {
                return null;
            }

            $action = $this->action;

            $dataprovider->$action($this->getModelParam());

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

    public function getModelParam() : ?string
    {
        $modelPlaceholder = strtolower(class_basename($this->getModel())); // eg. For Beat model it will be 'beat'


        return $this->parameters[$modelPlaceholder] ?? $this->parameters['id'] ?? null;
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
