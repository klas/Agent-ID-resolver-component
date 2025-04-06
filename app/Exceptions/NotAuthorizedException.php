<?php

namespace App\Exceptions;

class NotAuthorizedException extends ApiException
{
    protected $status = 403;
    protected $title = 'Not Authorized';

    public function __construct(?string $detail = null)
    {
        $this->detail = $detail ?? 'You are not authorized to perform this action.';

        parent::__construct($this->detail);
    }
}
