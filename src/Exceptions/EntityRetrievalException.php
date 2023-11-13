<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class EntityRetrievalException extends Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    { //zawsze wyrzucal message zapisany tutaj a moj customowy juz nie. teraz dziala> czy jest ok?
        if($message === '') {
            $message = 'There is no data in the database or there was a problem with retrieving the data you need.';
        }

        parent::__construct($message, $code, $previous);
    }

    public function getCustomMessage(): string
    {
        return $this->message;
    }
}