<?php

namespace App\Interfaces\CloudFileServices;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface CloudFileServiceInterface
{
    /**
     * Upload a file
     * @param
     * @param UploadedFile $file
     * @param string|null $parentDir
     * @param string|null $childDir
     * @return array
     */
    public function upload($owner_id, UploadedFile $file, ?string $parentDir = null, ?string $childDir = null): array;

    /**
     * Delete a file
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool;
}
