<?php

namespace Cmobi\RabbitmqBundle\Rpc\Exception;


class JsonRpcInvalidParamsException extends JsonRpcGenericErrorException
{
    const ERROR_CODE = -32602;

    public function __construct(\Exception $previous = null)
    {
        $message = 'Invalid params';
        parent::__construct($message, self::ERROR_CODE, $previous);
    }
}