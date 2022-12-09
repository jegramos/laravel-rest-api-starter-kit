<?php

namespace App\QueryFilters\User;

use App\QueryFilters\Filter;
use Illuminate\Database\Eloquent\Builder;

class Verified extends Filter
{
    public const FILTER_NAME = 'verified';

    /**
     * @inheritDoc
     */
    protected function applyFilter(Builder $builder): Builder
    {
        $verified = (bool) request('verified');

        if ($verified) {
            return $builder->whereNotNull('email_verified_at');
        }

        return $builder->whereNull('email_verified_at');
    }

    /**
     * @inheritDoc
     */
    protected function getFilterName(): string
    {
        return static::FILTER_NAME;
    }
}