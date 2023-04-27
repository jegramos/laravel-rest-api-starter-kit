<?php

namespace App\QueryFilters\User;

use App\QueryFilters\Filter;
use Illuminate\Database\Eloquent\Builder;

class Role extends Filter
{
    public const FILTER_NAME = 'role';

    /**
     * {@inheritDoc}
     */
    protected function applyFilter(Builder $builder): Builder
    {
        $role = request($this->getFilterName());

        if (! $role) {
            return $builder;
        }

        $tableName = (clone $builder)->getModel()->getTable();

        return $builder
            ->join('model_has_roles', 'model_has_roles.model_id', '=', "$tableName.id")
            ->where('model_has_roles.role_id', '=', $role);
    }

    /**
     * {@inheritDoc}
     */
    protected function getFilterName(): string
    {
        return static::FILTER_NAME;
    }
}
