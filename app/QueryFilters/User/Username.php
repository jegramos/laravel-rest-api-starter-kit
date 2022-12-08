<?php

namespace App\QueryFilters\User;

use App\QueryFilters\Filter;
use Illuminate\Database\Eloquent\Builder;

class Username extends Filter
{
    private const FILTER_NAME = 'username';

    protected function applyFilter(Builder $builder): Builder
    {
        $filterName = $this->getFilterName();
        $username = strtolower(request($filterName));

        return $builder->where('username', $username);
    }

    protected function getFilterName(): string
    {
        return static::FILTER_NAME;
    }
}
