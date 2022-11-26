<?php

namespace App\Services\FileUpload;

use Illuminate\Http\Request;
use Storage;

class S3UploadService
{
    /**
     * Upload file to S3
     *
     * @param $ownerId
     * @param Request $request
     * @return array
     */
    public function upload($ownerId, Request $request): array
    {
        $file = $request->file('photo');
        $filePath = "images/$ownerId/profile-pictures";

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
    public function getTmpUrl(string $path, int $seconds): string {
        return Storage::disk('s3')->temporaryUrl($path, now()->addSeconds($seconds));
    }
}
