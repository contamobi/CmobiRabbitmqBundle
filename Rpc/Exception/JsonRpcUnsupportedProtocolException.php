<?php

namespace Cmobi\RabbitmqBundle\Rpc\Exception;


class JsonRpcUnsupportedProtocolException extends JsonRpcGenericErrorException
{
    const ERROR_CODE = -32001;

    public function __construct(\Exception $previous = null)
    {
        $message = 'Unsupported protocol version';
        parent::__construct($message, self::ERROR_CODE, $previous);
    }
}