<?php

namespace App\QueryFilters;

use App\Interfaces\Database\SchemaServiceInterface;
use Illuminate\Contracts\Database\Query\Builder;

class Sort extends Filter
{
    protected function applyFilter(Builder $builder): Builder
    {
        $filterName = $this->getFilterName();

        if (!in_array(request($filterName), ['asc', 'desc'])) {
            return $builder;
        }

        $sortBy = request('sort_by');
        if (!$sortBy) {
            return $builder->orderBy('id', request($filterName));
        }

        $schemaService = resolve(SchemaServiceInterface::class);
        $tableName = (clone $builder)->getModel()->getTable();
        $columnExists = $schemaService->checkIfColumnExists($tableName, $sortBy);
        $sortBy = $columnExists ? $sortBy : 'id';
        return $builder->orderBy($sortBy, request($filterName));
    }

    protected function getFilterName(): string
    {
        return 'sort';
    }
}
