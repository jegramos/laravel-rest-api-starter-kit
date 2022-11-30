<?php

namespace App\Services\Database;

use App\Interfaces\Database\SchemaServiceInterface;
use Cache;
use Schema;

class SchemaService implements SchemaServiceInterface
{
    /** @inheritDoc */
    public function getAllColumns(string $tableName): array
    {
        return Cache::rememberForever($this->getAllColumnsCacheKey($tableName), function () use ($tableName) {
            return Schema::getColumnListing($tableName);
        });
    }

    /** @inheritDoc */
    public function checkIfColumnExists(string $tableName, string $columnName): bool
    {
        $tableNames = $this->getAllColumns($tableName);
        return in_array($columnName, $tableNames);
    }

    /** @inheritDoc */
    public function getAllColumnNamesExcept(string $tableName, array $excludedColumns): array
    {
        $allColumns = $this->getAllColumns($tableName);
        return array_diff($allColumns, $excludedColumns);
    }

    /**
     * Returns the cache key for fetching all the column names of database table
     *
     * @param $tableName
     * @return string
     */
    private function getAllColumnsCacheKey($tableName): string
    {
        // Returns the project's absolute path for the migration folder
        // ex. '/Users/jegramos/projects/sunrise-project/database/migrations'
        $migrationPath = database_path('migrations');

        // Get the Unix timestamp of when this file type was last modified
        $lastModified = filemtime($migrationPath);

        // This key will change when the contents of the migration folder is modified
        // e.x. When a migration file (or any file) is added, deleted, or renamed.
        // This will not change if the CONTENTS of the file is edited
        return "schema:$lastModified:$tableName:columns";
    }
}
