<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Paginator;

class CursorPaginator extends Paginator
{
    public function paginate(array $paginationData): mixed
    {
        $perPage = $paginationData['per_page'] ?? 15;
        $columns = ['*'];
        $cursorName = 'cursor';
        $cursor = $paginationData['cursor'] ?? null;

        return $this->dataprovider->cursorPaginate($perPage, $columns, $cursorName, $cursor);
    }
}
