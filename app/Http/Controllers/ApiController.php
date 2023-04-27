<?php

namespace App\Http\Controllers;

use App\Enums\ApiErrorCode;
use Illuminate\Http\JsonResponse;

abstract class ApiController extends Controller
{
    /**
     * Return a success JSON success response.
     */
    protected function success(?array $data, int $statusCode, array $headers = []): JsonResponse
    {
        $data = $data ?? [];

        $results = array_merge(['success' => true], $data);

        return response()->json($results, $statusCode, $headers);
    }

    /**
     * Return a formatted JSON error response
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

        if (! empty($errors)) {
            $results['errors'] = $errors;
        }

        return response()->json($results, $statusCode, $headers);
    }
}
