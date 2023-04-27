<?php

namespace App\QueryFilters\Generic;

use App\QueryFilters\Filter;
use Illuminate\Database\Eloquent\Builder;

class Active extends Filter
{
    private const FILTER_NAME = 'active';

    protected function applyFilter(Builder $builder): Builder
    {
        $filterName = $this->getFilterName();

        return $builder->where('active', (bool) request($filterName));
    }

    protected function getFilterName(): string
    {
        return static::FILTER_NAME;
    }
}
