<?php

namespace App\QueryFilters;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;

class Active extends Filter
{
    public const FILTER_NAME = 'active';

    protected function applyFilter(Builder $builder): Builder
    {
        $filterName = $this->getFilterName();
        return $builder->where('active', request($filterName));
    }

    protected function getFilterName(): string
    {
        return static::FILTER_NAME;
    }
}
