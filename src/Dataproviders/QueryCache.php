<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Dataproviders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class QueryCache
{
    public static string $prefix = 'toolbox:';

    public function remember(Builder $query, string $method, array $args, $ttl, callable $callback)
    {
        $key = $this->key($query, $method, $args);
        $tags = $this->tags($query);

        return Cache::tags($tags)->remember($key, $ttl, $callback);
    }

    protected function key(Builder $query, string $method, array $args): string
    {
        return hash('sha512', implode('|', [
            self::$prefix,
            $query->getModel()::class,
            $method,
            $this->sql($query),
            json_encode($args)
        ]));
    }

    protected function tags(Builder $query): array
    {
        $tables = [];

        $base = $query->getQuery();

        // Main table
        $tables[] = $base->from;

        // Joins
        if ($base->joins) {
            foreach ($base->joins as $join) {
                $tables[] = $join->table;
            }
        }

        return collect($tables)
            ->map(fn ($table) => self::$prefix . $table)
            ->unique()
            ->toArray()
        ;
    }

    protected function sql(Builder $query): string
    {
        $sql = $query->toSql();

        $bindings = collect($query->getBindings())
            ->map(fn ($binding) => is_numeric($binding)
                ? $binding
                : "'" . addslashes((string)$binding) . "'"
            )
            ->toArray();

        return vsprintf(str_replace('?', '%s', $sql), $bindings);
    }
}
