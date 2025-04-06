<?php

namespace App\Exceptions;

class ResourceNotFoundException extends ApiException
{
    protected $status = 404;
    protected $title = 'Resource Not Found';

    public function __construct(string $resource = 'resource', ?string $detail = null)
    {
        $this->detail = $detail ?? "The requested {$resource} could not be found.";

        parent::__construct($this->detail);
    }
}
