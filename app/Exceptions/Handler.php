<?php

namespace App\Exceptions;

use App\Services\ErrorLogService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function __construct(protected ErrorLogService $errorLogService, Container $container)
    {
        parent::__construct($container);
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            $this->errorLogService->logException($e);
        });

        // Register a renderable to format JSON API compliant responses
        $this->renderable(function (Throwable $e, $request) {
            // Our custom exceptions already know how to render themselves
            if ($e instanceof ApiException) {
                return $e->render();
            }

            // Return a JSON API compliant response for common exceptions
            if ($this->shouldReturnJson($request, $e)) {
                return $this->formatJsonApiResponse($e);
            }

            return null; // Let the default handler manage non-JSON responses
        });
    }

    /**
     * Format exception as JSON API compliant response
     */
    private function formatJsonApiResponse(Throwable $e): JsonResponse
    {
        $error = [
            'status' => 500,
            'title' => 'Internal Server Error',
            'detail' => config('app.debug') ? $e->getMessage() : 'An unexpected error occurred.',
        ];

        // Handle specific exception types
        if ($e instanceof ValidationException) {
            $errors = [];

            foreach ($e->validator->errors()->toArray() as $field => $messages) {
                foreach ($messages as $message) {
                    $errors[] = [
                        'status' => (string) 422,
                        'title' => 'Validation Failed',
                        'detail' => $message,
                        'source' => [
                            'pointer' => "/data/attributes/{$field}",
                        ],
                    ];
                }
            }

            return response()->json(['errors' => $errors], 422);
        }

        if ($e instanceof AuthenticationException) {
            $error['status'] = 401;
            $error['title'] = 'Unauthenticated';
            $error['detail'] = 'You are not authenticated to perform this action.';
        }

        if ($e instanceof ModelNotFoundException) {
            $error['status'] = 404;
            $error['title'] = 'Resource Not Found';
            $error['detail'] = 'The requested resource could not be found.';
        }

        if ($e instanceof NotFoundHttpException) {
            $error['status'] = 404;
            $error['title'] = 'Not Found';
            $error['detail'] = 'The requested resource does not exist.';
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            $error['status'] = 405;
            $error['title'] = 'Method Not Allowed';
            $error['detail'] = 'The HTTP method is not supported for this endpoint.';
        }

        // Log the error for server errors
        if ($error['status'] >= 500) {
            $this->errorLogService->logException($e);
        }

        return response()->json(['errors' => [$error]], $error['status']);
    }
}
