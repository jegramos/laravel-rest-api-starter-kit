<?php

namespace App\Exceptions;

use App\Enums\ApiErrorCode;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
    public function register()
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
                    ['message' => 'Route not found', 'error_code' => ApiErrorCode::UNKNOWN_ROUTE, 'errors' => null],
                    Response::HTTP_NOT_FOUND
                );
                break;
                // if we hit the app-level rate-limit
            case $e instanceof ThrottleRequestsException:
                $response = response()->json(
                    ['message' => 'Too many requests', 'error_code' => ApiErrorCode::RATE_LIMIT, 'errors' => null],
                    Response::HTTP_TOO_MANY_REQUESTS
                );
                break;
                // if we throw a validation error
            case $e instanceof ValidationException:
                $response = response()->json(
                    [
                        'message' => 'A validation error has occurred',
                        'error_code' => ApiErrorCode::VALIDATION_ERROR,
                        'errors' => $this->transformErrors($e)
                    ],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
                break;
                // if we throw an authentication error
            case $e instanceof AuthenticationException:
                $response = response()->json(
                    [
                        'message' => 'Authentication error',
                        'error_code' => ApiErrorCode::UNAUTHORIZED_ERROR,
                        'errors' => null
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
                break;
                // if a model is not found (e.g. from Model::findOrFail)
            case $e instanceof ModelNotFoundException:
                $response = response()->json(
                    [
                        'message' => 'Resource not found',
                        'error_code' => ApiErrorCode::RESOURCE_NOT_FOUND,
                        'errors' => null
                    ],
                    Response::HTTP_NOT_FOUND
                );
                break;
                // TODO: Reporting
                // if we f** up somewhere else
            default:
                $errorMessage = $e->getMessage();
                Log::error('An error was encountered',
                    ['error_message' => $e->getMessage(), 'error' => $e->getTraceAsString()]
                );

                if (app()->environment('production')) {
                    $errorMessage = 'An unknown error has occurred';
                }

                $response = response()->json(
                    ['message' => $errorMessage, 'error_code' => ApiErrorCode::SERVER_ERROR, 'errors' => null],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
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
