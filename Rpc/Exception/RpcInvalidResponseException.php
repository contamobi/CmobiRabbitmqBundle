<?php

namespace Cmobi\RabbitmqBundle\Rpc\Exception;


class RpcInvalidResponseException extends RpcGenericErrorException
{
    const ERROR_CODE = -32050;

    public function __construct($message = null, \Exception $previous = null)
    {
        if (is_null($message)) {
            $message = 'Invalid Response';
        }
        parent::__construct($message, self::ERROR_CODE, $previous);
    }
}