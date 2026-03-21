<?php

declare(strict_types=1);

namespace Toolbox\Paginator;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class Paginator
{
    protected array $allowedTypes = ['paginate', 'simplePaginate', 'cursorPaginate'];

    public function __construct(protected Builder $query)
    {
        
    }

    public static function make(Builder $query): self
    {
        return new self($query);
    }

    public function paginate(array $paginationData): LengthAwarePaginator|CursorPaginator
    {
        $type = $paginationData['type'] ?? 'paginate';
        $type = in_array($type, $this->allowedTypes) ? $type : 'paginate';

        $paginator = match ($type) {
            'paginate' => new DefaultPaginator($this->query),
            'simplePaginate' => new SimplePaginator($this->query),
            'cursorPaginate' => new CursorPaginator($this->query),
        };

        return $paginator->paginate($paginationData);
    }
}