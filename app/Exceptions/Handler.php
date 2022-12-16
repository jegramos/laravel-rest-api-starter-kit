<?php

namespace App\Exceptions;

use App\Enums\ApiErrorCode;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Log;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Modify some of Laravel's default error responses
     *
     * @param Request $request
     * @param Throwable $e
     * @return Response
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        if (!$request->is('api/*')) {
            return parent::render($request, $e);
        }

        return $this->createApiErrorResponse($e);
    }

    /**
     * Create a JSON error response based on Exception
     *
     * @param Throwable $e
     * @return JsonResponse
     */
    private function createApiErrorResponse(Throwable $e): JsonResponse
    {
        switch ($e) {
            // if route is not found
            case $e instanceof NotFoundHttpException:
            case $e instanceof MethodNotAllowedHttpException:
                $response = response()->json(
                    ['success' => false, 'message' => 'Route not found', 'error_code' => ApiErrorCode::UNKNOWN_ROUTE],
                    Response::HTTP_NOT_FOUND
                );
                break;
                // if we hit the app-level rate-limit
            case $e instanceof ThrottleRequestsException:
                $response = response()->json(
                    ['success' => false, 'message' => 'Too many requests', 'error_code' => ApiErrorCode::RATE_LIMIT],
                    Response::HTTP_TOO_MANY_REQUESTS
                );
                break;
                // if we throw a validation error
            case $e instanceof ValidationException:
                $response = response()->json(
                    [
                        'success' => false,
                        'message' => 'A validation error has occurred',
                        'error_code' => ApiErrorCode::VALIDATION,
                        'errors' => $this->transformErrors($e)
                    ],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
                break;
                // if we throw an authentication error
            case $e instanceof AuthenticationException:
                $response = response()->json(
                    [
                        'success' => false,
                        'message' => 'Authentication error',
                        'error_code' => ApiErrorCode::UNAUTHORIZED,
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
                break;
            case $e instanceof UnauthorizedException: // Spatie Auth Exception
            case $e instanceof AuthorizationException: // Laravel Auth Exception
            case $e instanceof HttpException && $e->getStatusCode() === Response::HTTP_FORBIDDEN: // catch abort(403)
                $response = response()->json(
                    [
                        'success' => false,
                        'message' => $e->getMessage(),
                        'error_code' => ApiErrorCode::UNAUTHORIZED,
                    ],
                    Response::HTTP_FORBIDDEN
                );
                break;
                // if a model is not found (e.g. from Model::findOrFail)
            case $e instanceof ModelNotFoundException:
                $modelName = class_basename($e->getModel());
                $response = response()->json(
                    [
                        'success' => false,
                        'message' => "$modelName not found",
                        'error_code' => ApiErrorCode::RESOURCE_NOT_FOUND,
                    ],
                    Response::HTTP_NOT_FOUND
                );
                break;
            case $e instanceof PostTooLargeException:
                $response = response()->json(
                    [
                        'success' => false,
                        'message' => "Request is too large",
                        'error_code' => ApiErrorCode::PAYLOAD_TOO_LARGE,
                    ],
                    Response::HTTP_REQUEST_ENTITY_TOO_LARGE
                );
                break;
            default:
                // if we f** up somewhere else
                Log::error($e->getMessage(), ['stack_trace' => $e->getTraceAsString()]);

                $body = [
                    'message' => $e->getMessage(),
                    'error_code' => ApiErrorCode::SERVER,
                    'stack_trace' => $e->getTraceAsString(),
                ];

                if (app()->environment('production')) {
                    $body['message'] = 'An unknown error has occurred';
                    unset($body['stack_trace']);
                }

                $response = response()->json($body, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * Transform validation error messages. We want consistent error formats.
     */
    private function transformErrors(ValidationException $exception): array
    {
        $errors = [];
        foreach ($exception->errors() as $field => $message) {
            $errors[] = ['field' => $field, 'messages' => $message];
        }

        return $errors;
    }
}
