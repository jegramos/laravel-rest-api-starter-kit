<?php

namespace App\QueryFilters;

use Illuminate\Contracts\Database\Query\Builder;

class Sort extends Filter
{
    protected function applyFilter(Builder $builder): Builder
    {
        $filterName = $this->getFilterName();

        if (!in_array(request($filterName), ['asc', 'desc'])) {
            return $builder;
        }

        return $builder->orderBy('created_at', request($filterName));
    }

    protected function getFilterName(): string
    {
        return 'sort';
    }
}
