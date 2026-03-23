<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Models\Traits;

use Illuminate\Support\Facades\Cache;
use Triploide\Toolbox\Dataproviders\QueryCache;

trait HasCache
{
    /**
     * Boot function from Laravel.
     */
    protected static function bootHasCache(): void
    {
        static::saved(fn () => Cache::tags([ QueryCache::$prefix . (new static)->getTable() ])->flush());
        static::deleted(fn () => Cache::tags([ QueryCache::$prefix . (new static)->getTable() ])->flush());
    }
}