<?php

namespace App\QueryFilters;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    /**
     * The main function of a Laravel-implemented Pipe
     *
     * @throws Exception
     */
    public function handle(Builder $request, Closure $next): Builder
    {
        if (!request()->has($this->getFilterName())) {
            return $next($request);
        }

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
