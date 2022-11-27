<?php

namespace App\Interfaces\CloudFileServices;

interface CanCreateUrlTmpInterface
{
    /**
     * Generate a URL available by X seconds
     *
     * @param $path
     * @param int $timeLimit - time before the URL expires (in seconds)
     */
    public function generateTmpUrl($path, int $timeLimit): string;
}
