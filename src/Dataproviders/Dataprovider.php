<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Dataproviders;

use Triploide\Toolbox\Helpers\Data;
use Triploide\Toolbox\Pathfinders\Pathfinder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Triploide\Toolbox\Managers\Manager;
use Triploide\Toolbox\Managers\ModelManager;
use Triploide\Toolbox\Managers\FilterManager;
use Triploide\Toolbox\Paginator\Paginator;

/**
 *
 * @property \DateTimeInterface|\DateInterval|int|null $cache_expiration
 * @method \DateTimeInterface|\DateInterval|int|null cache_expiration()
 * 
 */

class Dataprovider
{
    use Manager;
    use FilterManager;
    use ModelManager;

    protected Builder $query;
    protected Data $data;

    public function __construct(array $data = [], ?Model $model = null)
    {
        $this->setModel($model);
        $this->data = new Data($data);
        $this->setQuery();
    }

    public function setModel(?Model $model): void
    {
        if ($model) {
            $this->modelInstance = $model;
        }
    }

    protected function getPathfinder(): Pathfinder
    {
        return Pathfinder::create($this::class, 'Dataproviders', 'Dataprovider');
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function query(): Builder
    {
        return $this->query;
    }

    public function setQuery(): void
    {
        $this->query = $this->getModel()->newQuery();
    }

    public function fetchOne()
    {
        return $this->execute('first');
    }

    public function fetchOneOrFail()
    {
        return $this->execute('firstOrFail');
    }

    public function fetchAll()
    {
        if (request()->has('pagination')) {
            return $this->makePagination(request()->input('pagination'));
        } else {
            return $this->execute('get');
        }
    }

    public function makePagination(array $paginationData): mixed
    {
        return Paginator::make($this)->paginate($paginationData);
    }   

    /**
     * This method can be used to set up any necessary state or configurations before executing the query.
     * It can be overridden in child classes to customize the behavior of the dataprovider.
     * 
     * @return Dataprovider
     */
    public function setup(): Dataprovider
    {
        return $this;
    }

    /**
     * Get an instance of the QueryCache class, which is responsible for caching query results.
     * 
     * @return QueryCache
     */
    public function cache(): QueryCache
    {
        return new QueryCache();
    }

    /**
     * Determine the time-to-live (TTL) for cached query results. This method can be overridden in child classes to specify a custom TTL value.
     * 
     * @return Carbon
     */
    protected function cacheTtl(): Carbon
    {
        $fiveDays = 60 * 24 * 5; // 5 days in minutes

        return Carbon::now()->addMinutes(config('toolbox.cache.ttl', $fiveDays));
    }

    protected function useCache(): bool
    {
        return config('toolbox.cache.enabled', false);
    }

    protected function execute($method, $args = [])
    {
        $this->setup();

        if ($this->useCache()) {
            return $this->cache()->remember(
                $this->query(),
                $method,
                [...$args],
                $this->cacheTtl(),
                fn () => $this->query()->$method(...$args)
            );
        }

        return $this->query()->$method(...$args);
    }

    // Magic Methods
    public function __call($method, $args)
    {
        if (!method_exists($this->query, $method)) {
            throw new \BadMethodCallException("Method [$method] does not exist.");
        }

        $result = $this->execute($method, $args);

        return $result === $this->query ? $this : $result;
    }
}
