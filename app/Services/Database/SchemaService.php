<?php

namespace App\Services\Database;

use App\Interfaces\Database\SchemaServiceInterface;
use Schema;

class SchemaService implements SchemaServiceInterface
{
    public function getAllColumns(string $tableName): array
    {
        return Schema::getColumnListing($tableName);
    }

    public function checkIfColumnExists(string $tableName, string $columnName): bool
    {
        $tableNames = Schema::getColumnListing($tableName);
        return in_array($columnName, $tableNames);
    }
}
