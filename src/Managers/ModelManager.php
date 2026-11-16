<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Managers;

use Illuminate\Database\Eloquent\Model;
use Triploide\Toolbox\Resolvers\ModelResolver;

/**
 *
 * @property string $model - Fully qualified name of the model class (e.g. App\Models\Post)
 * @method string model() - Method that returns the fully qualified name of the model class (e.g. App\Models\Post)
 * @property string $modelPath - Path of the model class relative to the app folder (e.g. Models, Entities, etc.)
 * @method string modelPath() - Method that returns the path of the model class relative to the app folder (e.g. Models, Entities, etc.)
 * 
 */

trait ModelManager
{
    /**
     * The model instance is stored in a private property to avoid multiple instantiations of the same model during the request lifecycle.
     */
    private ?Model $modelInstance = null;

    /**
     * Get the model instance.
     * 
     * @return Model
     */
    final protected function getModel(): Model
    {
        if (!$this->modelInstance)
        {
            $modelClass = $this->resolveClass('model');

            $this->modelInstance = new $modelClass;
        }

        return $this->modelInstance;
    }

    /**
     * @return string
     */
    private function getModelFullyQualifiedName(): string
    {
        $resolver = new ModelResolver($this->getPathFinder()); // TODO: Reduce coupling

        return $resolver->getFullyQualifiedName('model');
    }
}
