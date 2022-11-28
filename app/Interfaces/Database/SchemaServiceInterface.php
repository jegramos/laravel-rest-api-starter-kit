<?php

namespace App\Interfaces\Database;

use Illuminate\Contracts\Database\Query\Builder;

interface SchemaServiceInterface
{
    /**
     * Get all columns of a table
     *
     * @param string $tableName
     * @return array
     */
    public function getAllColumns(string $tableName): array;

    /**
     * Check a column exists
     *
     * @param Builder $builder
     * @param string $columnName
     * @return bool
     */
    public function checkIfColumnExists(Builder $builder, string $columnName): bool;
}
