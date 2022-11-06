<?php

namespace App\Http\Controllers;

use App\Enums\ApiErrorCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

abstract class ApiController extends Controller
{
    /**
     * Return a success JSON success response.
     *
     * @param $data
     * @param int $statusCode
     * @param array $headers
     *
     * @return JsonResponse
     */
    protected function success($data, int $statusCode, array $headers = []): JsonResponse
    {
        return response()->json($data, $statusCode, $headers);
    }

    /**
     * Return a formatted JSON error response
     *
     * @param string $message
     * @param int $statusCode
     * @param ApiErrorCode|null $errorCode
     * @param array|null $errors
     * @param array $headers
     * @return JsonResponse
     */
    protected function error(
        string $message,
        int $statusCode,
        ApiErrorCode $errorCode = null,
        array $errors = null,
        array $headers = []
    ): JsonResponse
    {
        $data = [
            'error_code' => $errorCode,
            'message' => $message,
            'errors' => $errors
        ];

        $logErrorMsg = $errors ? $message . ' - ' . implode('|', $errors) : $message;

        if ($statusCode >= 500) {
            Log::error($logErrorMsg);

            // Hide the actual error from the client
            $data['errors'] = null;
        }

        return response()->json($data, $statusCode, $headers);
    }
}
