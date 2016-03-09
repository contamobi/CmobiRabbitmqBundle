<?php

namespace Cmobi\RabbitmqBundle\Rpc\Exception;


class JsonRpcInternalErrorException extends JsonRpcGenericErrorException
{
    const ERROR_CODE = -32603;

    public function __construct(\Exception $previous = null)
    {
        $message = 'Internal error';
        parent::__construct($message, self::ERROR_CODE, $previous);
    }
}