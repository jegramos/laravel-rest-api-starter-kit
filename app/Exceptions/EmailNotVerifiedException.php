<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class EmailNotVerifiedException extends Exception
{
    public string $email;

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
