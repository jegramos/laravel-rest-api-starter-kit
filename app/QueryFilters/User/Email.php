<?php

namespace App\QueryFilters\User;

use App\QueryFilters\Filter;
use Illuminate\Database\Eloquent\Builder;

class Email extends Filter
{
    private const FILTER_NAME = 'email';

    /**
     * @inheritDoc
     */
    protected function applyFilter(Builder $builder): Builder
    {
        $filterName = $this->getFilterName();
        $email = strtolower(request($filterName));

        return $builder->where('email', $email);
    }

    /**
     * @inheritDoc
     */
    protected function getFilterName(): string
    {
        return static::FILTER_NAME;
    }
}
