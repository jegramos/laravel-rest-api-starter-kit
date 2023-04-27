<?php

namespace App\Interfaces\Database;

interface SchemaServiceInterface
{
    /**
     * Get all the columns in a database table
     */
    public function getAllColumns(string $tableName): array;

    /**
     * Get all the tables from the database
     */
    public function getAllTables(): array;

    /**
     * Check if a column exists in a database table
     */
    public function checkIfColumnExists(string $tableName, string $columnName): bool;

    /**
     * Check if a given table exists
     */
    public function checkIfTableExists(string $tableName): bool;

    /**
     * Get all column names of a table except
     */
    public function getAllColumnNamesExcept(string $tableName, array $excludedColumns): array;
}
