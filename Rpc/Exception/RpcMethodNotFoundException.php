<?php

namespace Cmobi\RabbitmqBundle\Rpc\Exception;


class RpcMethodNotFoundException extends RpcGenericErrorException
{
    const ERROR_CODE = -32601;

    public function __construct(\Exception $previous = null)
    {
        $message = 'Method not found';
        parent::__construct($message, self::ERROR_CODE, $previous);
    }
}