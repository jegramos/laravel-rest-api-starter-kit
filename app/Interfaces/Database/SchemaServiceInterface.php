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
     * Check if a column exists
     *
     * @param string $tableName
     * @param string $columnName
     * @return bool
     */
    public function checkIfColumnExists(string $tableName, string $columnName): bool;
}
