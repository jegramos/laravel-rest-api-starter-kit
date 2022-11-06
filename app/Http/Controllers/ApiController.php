<?php

namespace App\Http\Controllers;

use App\Enums\ApiErrorCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

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
        $successMessage = ['success' => true, 'data' => $data];
        return response()->json($successMessage, $statusCode, $headers);
    }

    /**
     * Return a formatted JSON error response
     *
     * @param string $message
     * @param int $statusCode
     * @param ApiErrorCode|null $errorCode
     * @param array $errors
     * @param array $headers
     * @return JsonResponse
     */
    protected function error(
        string $message,
        int $statusCode,
        ApiErrorCode $errorCode = null,
        array $errors = [],
        array $headers = []
    ): JsonResponse {
        $errorMessage = [
            'success' => false,
            'error_code' => $errorCode,
            'error_message' => $message,
            'errors' => $errors
        ];

        $logErrorMsg = $errors ? $message . ' - ' . implode('|', $errors) : $message;

        if ($statusCode >= Response::HTTP_INTERNAL_SERVER_ERROR) {
            Log::error($logErrorMsg);
        }

        return response()->json($errorMessage, $statusCode, $headers);
    }
}
