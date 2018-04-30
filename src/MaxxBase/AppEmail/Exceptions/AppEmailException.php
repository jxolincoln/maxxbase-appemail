<?php

namespace MaxxBase\AppEmail\Exceptions;


class AppEmailException extends \Exception
{
    protected $message;
    protected $code;
    protected $file;
    protected $line;

    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message = "", $code, $previous);
    }

}