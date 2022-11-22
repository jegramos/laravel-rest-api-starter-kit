<?php

namespace App\Http\Controllers;

use App\Enums\ApiErrorCode;
use App\Enums\PaginationType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use PaginationHelper;

abstract class ApiController extends Controller
{
    /**
     * Return a success JSON success response.
     *
     * @param array $data
     * @param int $statusCode
     * @param array $headers
     *
     * @return JsonResponse
     */
    protected function success(array $data, int $statusCode, array $headers = []): JsonResponse
    {
        $results = ['success' => true, 'data' => $data];
        return response()->json($results, $statusCode, $headers);
    }

    /**
     * Return a success JSON success response with pagination
     *
     * @param PaginationType $type
     * @param array $data
     * @param int $statusCode
     * @param array $headers
     *
     * @return JsonResponse
     */
    protected function successWithPagination(
        PaginationType $type,
        array $data,
        int $statusCode,
        array $headers = []
    ): JsonResponse {
        if ($type === PaginationType::LENGTH_AWARE) {
            $results = PaginationHelper::formatLengthAwarePagination($data);
        }

        switch ($type) {
            case PaginationType::LENGTH_AWARE:
                $results = PaginationHelper::formatLengthAwarePagination($data);
                break;
            case PaginationType::SIMPLE:
                $results = PaginationHelper::formatSimplePagination($data);
                break;
            case PaginationType::CURSOR:
                $results = PaginationHelper::formatCursorPagination($data);
                break;
        }

        $results['success'] = true;
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
        $errorMessage = [
            'success' => false,
            'error_code' => $errorCode,
            'error_message' => $message,
            'errors' => $errors
        ];

        return response()->json($errorMessage, $statusCode, $headers);
    }
}
