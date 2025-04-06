<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddRequestIdHeader
{
    public function handle(Request $request, Closure $next): Response
    {
        // Generate request ID if not already present
        $requestId = $request->header('X-Request-ID');

        if (! $requestId) {
            $requestId = $this->generateRequestId();
            $request->headers->set('X-Request-ID', $requestId);
        }

        // Process the request
        $response = $next($request);

        // Add request ID to response for client-side tracking
        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }

    protected function generateRequestId(): string
    {
        return (string) str()->uuid();
    }
}
