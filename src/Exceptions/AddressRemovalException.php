<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;
class AddressRemovalException extends Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        $message = 'Something went wrong. Possibly Address Type is incorrect or such address does not exist';
        parent::__construct($message, $code, $previous);
    }

}