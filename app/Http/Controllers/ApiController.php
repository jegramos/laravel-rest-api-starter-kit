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
     * @param array $data
     * @param int $statusCode
     * @param array $headers
     * @param PaginationType|null $paginationType
     * @return JsonResponse
     */
    protected function success(
        array $data,
        int $statusCode,
        array $headers = [],
        ?PaginationType $paginationType = null
    ): JsonResponse {
        switch ($paginationType) {
            case PaginationType::LENGTH_AWARE:
                $results = array_merge(['success' => true], PaginationHelper::formatLengthAwarePagination($data));
                break;
            case PaginationType::SIMPLE:
                $results = array_merge(['success' => true], PaginationHelper::formatSimplePagination($data));
                break;
            case PaginationType::CURSOR:
                $results = array_merge(['success' => true], PaginationHelper::formatCursorPagination($data));
                break;
            default:
                $results = ['success' => true, 'data' => $data];
                break;
        }

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
