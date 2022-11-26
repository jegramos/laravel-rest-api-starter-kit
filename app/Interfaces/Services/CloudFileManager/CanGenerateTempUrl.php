<?php

namespace App\Interfaces\Services\CloudFileManager;

interface CanGenerateTempUrl
{
    /**
     * Generate a temporary URL for x seconds
     *
     * @param string $path
     * @param int $seconds
     * @return string
     */
    public function getTmpUrl(string $path, int $seconds): string;
}
