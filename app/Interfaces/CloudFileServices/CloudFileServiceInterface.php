<?php

namespace App\Interfaces\CloudFileServices;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface CloudFileServiceInterface
{
    /**
     * Upload a file
     * @param $ownerId
     * @param UploadedFile $file
     * @param string|null $parentDir
     * @param string|null $childDir
     * @return array
     */
    public function upload($ownerId, UploadedFile $file, ?string $parentDir = null, ?string $childDir = null): array;

    /**
     * Delete a file
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool;

    /**
     * Generate a URL available by X seconds
     *
     * @param $path
     * @param int $timeLimit - time before the URL expires (in seconds)
     */
    public function generateTmpUrl($path, int $timeLimit): string;
}
