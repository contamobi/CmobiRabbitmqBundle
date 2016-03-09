<?php

namespace Cmobi\RabbitmqBundle\Rpc\Exception;


class JsonRpcParserErrorException extends JsonRpcGenericErrorException
{
    const ERROR_CODE = -32700;

    public function __construct(\Exception $previous = null)
    {
        $message = 'Parse error';
        parent::__construct($message, self::ERROR_CODE, $previous);
    }
}