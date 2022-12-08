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
     * Get all the tables from the database
     *
     * @return array
     */
    public function getAllTables(): array;

    /**
     * Check if a column exists in a database table
     *
     * @param string $tableName
     * @param string $columnName
     * @return bool
     */
    public function checkIfColumnExists(string $tableName, string $columnName): bool;

    /**
     * Check if a given table exists
     *
     * @param string $tableName
     * @return bool
     */
    public function checkIfTableExists(string $tableName): bool;

    /**
     * Get all column names of a table except
     *
     * @param string $tableName
     * @param array $excludedColumns
     * @return array
     */
    public function getAllColumnNamesExcept(string $tableName, array $excludedColumns): array;
}
