<?php

declare(strict_types=1);

namespace Toolbox\Paginator;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SimplePaginator extends Paginator
{
    public function paginate(array $paginationData): LengthAwarePaginator
    {
        $perPage = $paginationData['per_page'] ?? 15;
        $columns = ['*'];
        $pageName = 'page';
        $page = $paginationData['page'] ?? 1;

        return $this->query->simplePaginate($perPage, $columns, $pageName, $page);
    }
}