<?php

namespace Cmobi\RabbitmqBundle\Rpc\Exception;


abstract class RpcServerErrorException extends RpcGenericErrorException
{
    const ERROR_CODE = -32000;

    public function __construct($message = "Server error", \Exception $previous = null)
    {
        parent::__construct($message, self::validateCode(self::ERROR_CODE), $previous);
    }

    public function validateCode($code)
    {
        if ($code < -32000 || $code > -32099) {
            $this->message = "Server error";
            return self::ERROR_CODE;
        }

        return $code;
    }
}