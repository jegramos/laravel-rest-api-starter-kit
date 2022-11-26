<?php

namespace App\Interfaces\Services;

interface FileUploadServiceInterface
{
    public function upload($file, string $path = ''): array;

    public function delete(string $path): array;

    public function getPresignedUrl(): array;
}
