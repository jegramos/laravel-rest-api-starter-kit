<?php

namespace App\Http\Controllers;

use App\Enums\ApiErrorCode;
use App\Enums\PaginationType;
use Illuminate\Http\JsonResponse;
use PaginationHelper;

abstract class ApiController extends Controller
{
    /**
     * Return a success JSON success response.
     *
     * @param array|null $data
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    protected function success(?array $data, int $statusCode, array $headers = []): JsonResponse
    {
        $data = $data ?? [];

        $results = array_merge(['success' => true], $data);
        return response()->json($results, $statusCode, $headers);
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
        $results = [
            'success' => false,
            'error_code' => $errorCode,
            'error_message' => $message,
        ];

        if (!empty($errors)) {
            $results['errors'] = $errors;
        }

        return response()->json($results, $statusCode, $headers);
    }
}
