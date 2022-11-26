<?php

namespace App\QueryFilters;

use Closure;
use Exception;
use Illuminate\Contracts\Database\Query\Builder;

abstract class Filter
{
    /**
     * The main function of a Laravel-implemented Pipeline stage
     *
     * @throws Exception
     */
    public function handle(Builder $request, Closure $next): Builder
    {
        if (!request()->has($this->getFilterName())) {
            return $next($request);
        }

        /** @var Builder $builder */
        $builder = $next($request);

        return $this->applyFilter($builder);
    }

    /**
     * Get the filter name
     *
     * @return string
     */
    abstract protected function getFilterName(): string;

    /**
     * Apply the query filter
     *
     * @param Builder $builder
     * @return Builder
     */
    abstract protected function applyFilter(Builder $builder): Builder;
}
