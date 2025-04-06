<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Throwable;

class ErrorLogService
{
    /**
     * Log an exception with context
     */
    public function logException(Throwable $exception, array $additionalContext = []): void
    {
        $context = array_merge([
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode(),
            'trace' => $this->formatStackTrace($exception),
            'request_id' => request()->header('X-Request-ID') ?? uniqid('req-'),
            'user_id' => auth()->id() ?? 'guest',
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ], $additionalContext);

        // Log to appropriate channel based on severity
        if ($this->isCriticalException($exception)) {
            Log::channel('critical')->error($exception->getMessage(), $context);

            // Optional: Send alerts for critical errors
            // $this->alertService->sendCriticalErrorAlert($exception, $context);
        } else {
            Log::error($exception->getMessage(), $context);
        }
    }

    /**
     * Log a custom error
     */
    public function logError(string $message, array $context = [], string $level = 'error'): void
    {
        $enhancedContext = array_merge([
            'request_id' => request()->header('X-Request-ID') ?? uniqid('req-'),
            'user_id' => auth()->id() ?? 'guest',
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ], $context);

        Log::$level($message, $enhancedContext);
    }

    /**
     * Format stack trace for better readability
     */
    private function formatStackTrace(Throwable $exception): array
    {
        $formatted = [];
        $trace = $exception->getTrace();

        foreach ($trace as $index => $frame) {
            $formatted[] = [
                'file' => $frame['file'] ?? 'unknown',
                'line' => $frame['line'] ?? 0,
                'function' => ($frame['class'] ?? '').($frame['type'] ?? '').($frame['function'] ?? ''),
            ];
        }

        return $formatted;
    }

    /**
     * Determine if an exception is critical
     */
    private function isCriticalException(Throwable $exception): bool
    {
        $criticalExceptions = [
            'PDOException',
            'Illuminate\Database\QueryException',
            'Swift_TransportException',
            'ErrorException',
        ];

        foreach ($criticalExceptions as $criticalClass) {
            if ($exception instanceof $criticalClass) {
                return true;
            }
        }

        return false;
    }
}
