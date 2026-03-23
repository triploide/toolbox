<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Abstract class Filter.
 * 
 * This class provides a base structure for filtering query results.
 * It contains methods for applying filters and sorting to a query builder instance.
 */
abstract class Filter
{
    public function __construct(protected Builder $query)
    {
        
    }

    /**
     * Get the query builder instance.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(): Builder
    {
        return $this->query;
    }

    /**
     * Apply the given filters to the query.
     *
     * Iterates through the array of filters and applies each one to the query
     * if a method matching the filter name exists in the class.
     *
     * @param array $filters An associative array of filters where the keys are filter method names 
     *                       and the values are the filter parameters.
     * @return void
     */
    final public function apply(array $filters): void
    {
        foreach ($filters as $filter => $value) {
            if (method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }
    }

    /**
     * Apply the given sort to the query.
     *
     * On Filter class, as order_by_[column set as field]. allows camelcase. 
     * if no method found, orders by value recieved by default. Might cause query exception
     *
     * @param array $sort An associative array of with column and type values. (asc or desc)
     * @return void
     */
    public function orderBy(array $sort): void
    {
        collect($sort)->filter(fn($order) => $this->isSortAllowed($order['column']))->each(function ($order) {
            $method = (strpos($order['column'], '.') !== false) ? 'orderByJoin' : 'orderBy';
            $this->query->$method($order['column'], $order['dir']);
        });
    }

    /**
     * Get the available columns for sorting.
     * By default, it returns '*' which means all columns are allowed for sorting.
     * 
     * @return string|array Returns a string or an array of available columns for sorting.
     */
    public function availableSortColumns(): string|array
    {
        return '*';
    }

    /**
     * Check if the given column is allowed for sorting.
     * 
     * @param string $column The column name to check.
     * @return bool Returns true if the column is allowed for sorting, false otherwise.
     */
    public function isSortAllowed(string $column): bool
    {
        $availableColumns = $this->availableSortColumns();

        $availableColumns = is_string($availableColumns) ? explode(',', $availableColumns) : $availableColumns;

        return in_array($column, $availableColumns) || in_array('*', $availableColumns);
    }

    /**
     * Searches the given term in the query.
     *
     * If Methods doesnt exists, does nothing.
     *
     * @param string $searchTerm The search term to be applied to the query.
     * @return void
     */
    public function search(string $searchTerm): void
    {
        // Implement search logic in the child class if needed.
    }
}