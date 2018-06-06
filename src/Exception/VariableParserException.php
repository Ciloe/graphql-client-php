<?php

namespace GraphQLClientPhp\Exception;

class VariableParserException extends \Exception
{
    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct(string $message = "", int $code = 462)
    {
        parent::__construct($message, $code);
    }
}
