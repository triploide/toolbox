<?php

declare(strict_types=1);

namespace Toolbox\Paginator;

use Toolbox\Dataproviders\Dataprovider;

class Paginator
{
    protected array $allowedTypes = ['paginate', 'simplePaginate', 'cursorPaginate'];

    public function __construct(protected Dataprovider $dataprovider)
    {
        
    }

    public static function make(Dataprovider $dataprovideer): self
    {
        return new self($dataprovideer);
    }

    public function paginate(array $paginationData): mixed
    {
        $type = $paginationData['type'] ?? 'paginate';
        $type = in_array($type, $this->allowedTypes) ? $type : 'paginate';

        $paginator = match ($type) {
            'paginate' => new DefaultPaginator($this->dataprovider),
            'simplePaginate' => new SimplePaginator($this->dataprovider),
            'cursorPaginate' => new CursorPaginator($this->dataprovider),
        };

        return $paginator->paginate($paginationData);
    }
}
