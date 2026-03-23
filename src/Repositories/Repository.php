<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Triploide\Toolbox\Traits\HasEvents;
use Triploide\Toolbox\Helpers\Data;
use Triploide\Toolbox\Managers\Manager;
use Triploide\Toolbox\Managers\ModelManager;
use Triploide\Toolbox\Pathfinders\Pathfinder;
use Triploide\Toolbox\Resolvers\ActionResolver;

abstract class Repository
{
    use HasEvents;
    use Manager;
    use ModelManager;

    protected Data $data;
    protected ?Model $model = null;
    protected ?array $fillable = null;

    public function __construct(array $data = [], ?Model $model = null)
    {
        $this->data = new Data($data);
        $this->setModel($model);
    }

    public function setModel(?Model $model): void
    {
        if ($model) {
            $this->modelInstance = $model;
        }
    }

    protected function getPathfinder(): Pathfinder
    {
        return Pathfinder::create($this::class, 'Repositories', 'Repository');
    }

    /**
     * @param string $method - String with two possible values: store or update
     * @param bool $save_in_db - Indicates whether you should only create the model or also persist it in the database
     * @return Model
     */
    protected function persist(string $method, bool $save_in_db = true): Model
    {
        return DB::transaction(function () use ($method, $save_in_db) {
            $eventBefore = 'before' . ucfirst($method);
            $eventAfter = 'after' . ucfirst($method);

            // Before Events
            if ($save_in_db) {
                $this->$eventBefore();
                $this->beforeSave();
            }

            // Hydrate Model
            $this->getModel()->fill($this->data()->all());

            // Persist in DB
            if ($save_in_db) {
                $this->getModel()->save();
            }

            // After Events
            if ($save_in_db) {
                $this->$eventAfter();
                $this->afterSave();
            }

            // Response
            return $this->getModel();
        });
    }

    /**
     * @param Collection|array $data - Data for the creation of the model
     * @return Model
     */
    public function make(Collection|array $data = []): Model
    {
        $this->data()->replace($data);

        return $this->persist('store', save_in_db: false);
    }

    /**
     * @param Collection|array $data - Data for the persistence of the model
     * @return Model
     */
    public function store(Collection|array $data = []): Model
    {
        $action = $this->resolveAction('store');

        return app($action)->execute(
            $this->data()->merge($data),
            model: $this->getModel(),
            user: auth()->user() ?? auth('sanctum')->user() ?? null
        );
    }

    /**
     * @param Collection|array $data - Data for the update of the model
     * @return Model
     */
    public function update(Collection|array $data = []): Model
    {
        if (!$this->getModel() || !$this->getModel()->exists) {
            throw new \RuntimeException('Model must exist for update.');
        }

        $action = $this->resolveAction('update');

        return app($action)->execute(
            $this->data()->merge($data),
            model: $this->getModel(),
            user: auth()->user() ?? auth('sanctum')->user() ?? null
        );
    }

    /**
     * @param Collection|array $data - Data for the creation and/or persistence of the model
     * @return Model
     */
    public function save(Collection|array $data = []): Model
    {
        $this->data()->replace($data);

        $action = $this->getModel()->id ? 'update' : 'store';

        return $this->persist($action);
    }

    /**
     * @return bool
     */
    public function destroy(): bool
    {
        if (!$this->getModel() || !$this->getModel()->exists) {
            throw new \RuntimeException('Model must exist for update.');
        }

        return DB::transaction(function () {
            $this->beforeDelete();

            $action = $this->resolveAction('update');
            $deleted = app($action)->execute(
                model: $this->getModel(),
                user: auth()->user() ?? auth('sanctum')->user() ?? null
            );

            $this->afterDelete();

            return $deleted;
        });
    }

    /**
     * @return bool
     */
    public function masDestroy(): bool
    {
        $ids = $this->data('ids', []);

        return $this->getModel()
            ->newQuery()
            ->whereIn('id', $ids)
            ->delete()
        ;
    }

    /**
     * @param int|string $status
     * @return Model
     */
    public function transitionTo(int|string $status): Model
    {
        $column = $this->data('column');

        $this->getModel()->update([
            $column => $status
        ]);

        return $this->getModel();
    }

    /**
     * @return mixed
     */
    protected function data(?string $key = null, $default = null): mixed
    {
        return $key
            ? $this->data->get($key, $default)
            : $this->data
        ;
    }

    protected function resolveAction(string $action): string
    {
        $resolver = new ActionResolver($this->getPathfinder());

        return $resolver->getFullyQualifiedName($action);
    }
}
