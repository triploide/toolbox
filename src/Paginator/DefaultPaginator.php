<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Paginator;


class DefaultPaginator extends Paginator
{
    public function paginate(array $paginationData): mixed
    {
        $perPage = $paginationData['per_page'] ?? 15;
        $columns = ['*'];
        $pageName = 'page';
        $page = $paginationData['page'] ?? 1;
        $total = null;

        return $this->dataprovider->paginate($perPage, $columns, $pageName, $page, $total);
    }
}
