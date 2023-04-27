<?php

namespace App\Interfaces\CloudFileServices;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface CloudFileServiceInterface
{
    /**
     * Upload a file
     */
    public function upload($ownerId, UploadedFile $file, ?string $parentDir = null, ?string $childDir = null): array;

    /**
     * Delete a file
     */
    public function delete(string $path): bool;

    /**
     * Generate a URL available by X seconds
     *
     * @param  int  $timeLimit - time before the URL expires (in seconds)
     */
    public function generateTmpUrl($path, int $timeLimit): string;
}
