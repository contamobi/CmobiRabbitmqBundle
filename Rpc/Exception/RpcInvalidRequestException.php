<?php

namespace Cmobi\RabbitmqBundle\Rpc\Exception;


class RpcInvalidRequestException extends RpcGenericErrorException
{
    const ERROR_CODE = -32600;

    public function __construct($message = null,\Exception $previous = null)
    {
        if (is_null($message)) {
            $message = 'Invalid Request';
        }
        parent::__construct($message, self::ERROR_CODE, $previous);
    }
}