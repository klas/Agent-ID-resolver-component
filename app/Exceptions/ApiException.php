<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

abstract class ApiException extends Exception
{
    protected $status = 500;
    protected $title = 'Server Error';
    protected $detail;
    protected $source;

    /**
     * Get the HTTP status code for this exception.
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Get the error title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get detailed error information.
     */
    public function getDetail(): ?string
    {
        return $this->detail ?? $this->getMessage();
    }

    /**
     * Get the source of the error.
     */
    public function getSource(): ?array
    {
        return $this->source;
    }

    /**
     * Create a JSON response for this exception.
     */
    public function render(): JsonResponse
    {
        $error = [
            'status' => (string) $this->getStatus(),
            'title' => $this->getTitle(),
            'detail' => $this->getDetail(),
        ];

        if ($this->getSource()) {
            $error['source'] = $this->getSource();
        }

        return response()->json(['errors' => [$error]], $this->getStatus());
    }
}
