<?php

declare(strict_types=1);

namespace Toolbox\Paginator;

use Illuminate\Contracts\Pagination\CursorPaginator as PaginationCursorPaginator;

class CursorPaginator extends Paginator
{
    public function paginate(array $paginationData): PaginationCursorPaginator
    {
        $perPage = $paginationData['per_page'] ?? 15;
        $columns = ['*'];
        $cursorName = 'cursor';
        $cursor = $paginationData['cursor'] ?? null;

        return $this->query->cursorPaginate($perPage, $columns, $cursorName, $cursor);
    }
}