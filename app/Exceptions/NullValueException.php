<?php

namespace App\Http\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class NullValueException extends Exception
{
    public function __construct($message = 'Null value found', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        return response()->json([
            'error' => $this->getMessage(),
        ], ResponseAlias::HTTP_NOT_FOUND);
    }
}