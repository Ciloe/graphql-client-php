<?php

namespace GraphQLClientPhp\Exception;

class BadResponseException extends \Exception
{
    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct(string $message = "", int $code = 464)
    {
        parent::__construct($message, $code);
    }
}
