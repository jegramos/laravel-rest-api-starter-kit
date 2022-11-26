<?php

namespace App\Services\CloudFileManager;

use App\Interfaces\Services\CloudFileManager\CanGenerateTempUrl;
use App\Interfaces\Services\CloudFileManager\CloudFileManagerInterface;
use Illuminate\Http\UploadedFile;
use Storage;

class S3FileManager implements CloudFileManagerInterface, CanGenerateTempUrl
{
    /**
     * Upload file to S3
     *
     * @param $ownerId
     * @param UploadedFile $file
     * @param string|null $parentPath
     * @param string|null $childPath
     * @return array
     */
    public function upload($ownerId, UploadedFile $file, ?string $parentPath = null, ?string $childPath = null): array
    {
        $parentPath = !$parentPath ? '' : "$parentPath/";
        $childPath = !$childPath ? '' : "/$childPath";
        $filePath = $parentPath . $ownerId . $childPath;

        $path = Storage::disk('s3')->put($filePath, $file);
        $url = $this->getTmpUrl($path, 3 * 60);

        return [
            'path' => $path,
            'url' => $url,
            'owner_id' => $ownerId,
        ];
    }

    /**
     * Delete a file in S3 bucket
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        return Storage::disk('s3')->delete($path);
    }

    /**
     * Get pre-signed URL
     * @see https://docs.aws.amazon.com/AmazonS3/latest/userguide/ShareObjectPreSignedURL.html
     *
     * @param string $path
     * @param int $seconds
     * @return string
     */
    public function getTmpUrl(string $path, int $seconds): string
    {
        return Storage::disk('s3')->temporaryUrl($path, now()->addSeconds($seconds));
    }
}
