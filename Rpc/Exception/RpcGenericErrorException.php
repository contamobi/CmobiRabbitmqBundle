<?php

namespace Cmobi\RabbitmqBundle\Rpc\Exception;

abstract class RpcGenericErrorException extends \Exception
{
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        $error = [
            'code' => (int)$this->code,
            'message' => (string)$this->message
        ];

        return json_encode($error);
    }
}