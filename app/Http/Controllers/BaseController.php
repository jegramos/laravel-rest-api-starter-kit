<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BaseController extends Controller
{
    /**
     * Return a success JSON response.
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
}
