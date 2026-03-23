<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Dataproviders;

class CrudDataprovider extends Dataprovider
{
    /**
     * @return Dataprovider
     */
    public function index(): Dataprovider
    {
        $this->applyFilters();

        $this->applyOrderBy();

        $this->applySearch();
        
        return $this;
    }

    /**
     * @param int|string|null $id
     * @return Dataprovider
     */
    public function show($id = null): Dataprovider
    {
        if ($id) {
            $this->query()->where('id', $id);
        }

        return $this;
    }
}
