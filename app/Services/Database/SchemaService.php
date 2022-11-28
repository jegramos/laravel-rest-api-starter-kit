<?php

namespace App\Services\Database;

use App\Interfaces\Database\SchemaServiceInterface;
use Illuminate\Contracts\Database\Query\Builder;
use Schema;

class SchemaService implements SchemaServiceInterface
{

    public function getAllColumns(string $tableName): array
    {
        return Schema::getColumnListing($tableName);
    }

    public function checkIfColumnExists(Builder $builder, string $columnName): bool
    {
        $tableName = $builder->getModel()->getTable();
        $tableNames = Schema::getColumnListing($tableName);
        return in_array($columnName, $tableNames);
    }
}
