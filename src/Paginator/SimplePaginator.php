<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Paginator;


class SimplePaginator extends Paginator
{
    public function paginate(array $paginationData): mixed
    {
        $perPage = $paginationData['per_page'] ?? 15;
        $columns = ['*'];
        $pageName = 'page';
        $page = $paginationData['page'] ?? 1;

        return $this->dataprovider->simplePaginate($perPage, $columns, $pageName, $page);
    }
}
