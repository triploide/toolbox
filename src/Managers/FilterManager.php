<?php

declare(strict_types=1);

namespace Toolbox\Managers;

use Toolbox\Filters\Filter;

/**
 *
 * @property bool $useValidator
 * @method bool useValidator()
 * @property string $validator
 * @method string validator()
 * 
 */

trait FilterManager
{
    protected ?Filter $_filter = null;

    /**
     * The method applies the filters to the dataprovider if the "filters" parameter is present in the request and if the tool is enabled.
     * The filters are applied using the apply() method of the filter.
     * 
     * @return FilterManager
     */
    protected function applyFilters(): self
    {
        if ($this->mustApply('filters')) {
            $filters = $this->data->get('filters', []);

            if (count($filters) > 0 && $filter = $this->resolveFilter()) {
                $filter->apply($filters);
            }
        }

        return $this;
    }

    /**
     * The method applies the sorting to the dataprovider if the "sorts" parameter is present in the request and if the tool is enabled.
     * The sorting is applied using the orderBy() method of the filter.
     * 
     * @return FilterManager
    */
    protected function applyOrderBy(): self
    {
        if ($this->mustApply('sorts')) {
            $sorts = $this->data->get('sorts', []);

            if (count($sorts) > 0 && $filter = $this->resolveFilter()) {
                $filter->orderBy($sorts);
            }
        }

        return $this;
    }

    /**
     * The method applies the search to the dataprovider if the "search" parameter is present in the request and if the tool is enabled.
     * The search is applied using the search() method of the filter.
     *
     * @return self
     */
    protected function applySearch(): self
    {
        if ($this->mustApply('search')) {
            $search = trim($this->data->get('search', ''));

            if ($search && $filter = $this->resolveFilter()) {
                $filter->search($search);
            }
        }

        return $this;
    }

    /**
     * The method resolves the filter class using the resolveClass() method of the Manager trait.
     * The resolved class is cached in the $_filter property to avoid multiple resolutions.
     *
     * @return Filter|null
     */
    protected function resolveFilter(): ?Filter
    {
        return $this->_filter ??= $this->resolveClass('filter', fn ($fqn) => new $fqn($this->query));
    }
}