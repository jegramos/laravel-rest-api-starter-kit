<?php

namespace App\Services\CloudFileServices;

use App\Interfaces\CloudFileServices\CloudFileServiceInterface;
use Storage;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class S3FileService implements CloudFileServiceInterface
{
    /** {@inheritDoc} */
    public function upload($ownerId, UploadedFile $file, ?string $parentDir = null, ?string $childDir = null): array
    {
        $topPath = $parentDir ? "$parentDir/" : '';
        $childPath = $childDir ? "/$childDir" : '';
        $fullPath = $topPath.$ownerId.$childPath;

        $s3Path = Storage::disk('s3')->put($fullPath, $file);

        // Storage::put() returns false if un-successful
        if ($s3Path === false) {
            throw new UploadException('Unable to upload file to S3');
        }

        return [
            'owner_id' => $ownerId,
            'path' => $s3Path,
            'url' => $this->generateTmpUrl($s3Path, 60),
        ];
    }

    /** {@inheritDoc} */
    public function delete(string $path): bool
    {
        return Storage::disk('s3')->deleteDirectory($path);
    }

    /**
     * Generate a presigned URL
     *
     * @see https://docs.aws.amazon.com/AmazonS3/latest/userguide/ShareObjectPreSignedURL.html
     *
     * @param  int  $timeLimit (in seconds)
     */
    public function generateTmpUrl($path, int $timeLimit): string
    {
        return Storage::disk('s3')->temporaryUrl($path, now()->addSeconds($timeLimit));
    }
}
