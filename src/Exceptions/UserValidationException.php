<?php
declare(strict_types = 1);

namespace App\Exceptions;

use Exception;
use Throwable;

class UserValidationException extends Exception
{


    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        $message = 'Validation Failed! Sorry!';
        parent::__construct($message, $code,$previous);
    }




}