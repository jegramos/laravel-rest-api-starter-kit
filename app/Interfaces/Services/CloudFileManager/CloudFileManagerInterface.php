<?php

namespace App\Interfaces\Services\CloudFileManager;

use Illuminate\Http\UploadedFile;

interface CloudFileManagerInterface
{
    /**
     * Upload file to a cloud service
     *
     * @param $ownerId
     * @param UploadedFile $file
     * @param string|null $parentPath
     * @param string|null $childPath
     * @return array
     */
    public function upload($ownerId, UploadedFile $file, ?string $parentPath = null, ?string $childPath = null): array;

    /**
     * Delete a file from a cloud service
     *
     * @param string $path
     */
    public function delete(string $path): bool;
}
