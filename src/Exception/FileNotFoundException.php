<?php

namespace GraphQLClientPhp\Exception;

class FileNotFoundException extends \Exception
{
    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct(string $message = "", int $code = 461)
    {
        parent::__construct($message, $code);
    }
}
