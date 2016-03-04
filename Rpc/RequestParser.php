<?php

namespace Cmobi\CmobiRabbitmqBundle\Rpc;

use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\HttpFoundation\Request;

class RequestParser
{
    public function parse(AMQPMessage $message)
    {
        $request = new Request();

    }
}