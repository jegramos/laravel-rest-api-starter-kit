<?php

namespace App\Interfaces\Database;

interface SchemaServiceInterface
{
    /**
     * Get all the columns in a database table
     *
     * @param string $tableName
     * @return array
     */
    public function getAllColumns(string $tableName): array;

    /**
     * Check if a column exists in a database table
     *
     * @param string $tableName
     * @param string $columnName
     * @return bool
     */
    public function checkIfColumnExists(string $tableName, string $columnName): bool;
}
