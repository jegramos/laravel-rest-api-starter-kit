<?php

namespace App\Services\CloudFileServices;

use App\Interfaces\CloudFileServices\CanCreateUrlTmpInterface;
use App\Interfaces\CloudFileServices\CloudFileServiceInterface;
use Storage;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class S3FileService implements CloudFileServiceInterface, CanCreateUrlTmpInterface
{
    /** @inheritDoc */
    public function upload($owner_id, UploadedFile $file, ?string $parentDir = null, ?string $childDir = null): array
    {
        $topPath = $parentDir ? "$parentDir/" : '';
        $childPath = $childDir ? "/$childDir" : '';
        $fullPath = $topPath . $owner_id . $childPath;

        $s3Path = Storage::put($fullPath, $file);

        // Storage::put() returns false if un-successful
        if ($s3Path === false) {
            throw new UploadException('Unable to upload file to S3');
        }

        return [
            'owner_id' => $owner_id,
            'path' => $s3Path,
            'tmp_url' => $this->generateTmpUrl($s3Path, 60)
        ];
    }

    /** @inheritDoc */
    public function delete(string $path): bool
    {
        // TODO: Implement delete() method.
    }

    /**
     * Generate a presigned URL
     * @see https://docs.aws.amazon.com/AmazonS3/latest/userguide/ShareObjectPreSignedURL.html
     *
     * @param $path
     * @param int $timeLimit (in seconds)
     * @return string
     */
    public function generateTmpUrl($path, int $timeLimit): string
    {
        return Storage::temporaryUrl($path, now()->addSeconds($timeLimit));
    }
}
