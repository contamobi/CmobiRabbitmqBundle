<?php

namespace Cmobi\RabbitmqBundle\Rpc\Exception;


class RpcInternalErrorException extends RpcGenericErrorException
{
    const ERROR_CODE = -32603;

    public function __construct($message = null, \Exception $previous = null)
    {
        if (is_null($message)) {
            $message = 'Internal error';
        }
        parent::__construct($message, self::ERROR_CODE, $previous);
    }
}