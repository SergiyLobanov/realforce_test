<?php

namespace App\Exception;

use Exception;

class IncorrectRequestException extends Exception
{
    /**
     * IncorrectRequestException constructor.
     */
    public function __construct()
    {
        parent::__construct('Incorrect request format', 400);
    }
}