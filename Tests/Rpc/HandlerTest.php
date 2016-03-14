<?php

namespace Cmobi\RabbitmqBundle\Tests\Handler;

use Cmobi\RabbitmqBundle\Rpc\Handler;
use Cmobi\RabbitmqBundle\Rpc\Request\RpcRequestCollection;
use Cmobi\RabbitmqBundle\Rpc\Response\RpcResponseCollection;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use PhpAmqpLib\Message\AMQPMessage;

class HandlerTest extends BaseTestCase
{
    public function testHandle()
    {
        /**
         * @var RpcRequestCollection $requests
         */
        //$requests = $this->getMockBuilder('Cmobi\RabbitmqBundle\Rpc\Request\JsonRpcRequestCollection')->getMock();
        /**
         * @var RpcResponseCollection $responses
         */
       // $responses = $this->getMockBuilder('Cmobi\RabbitmqBundle\Rpc\Response\JsonRpcResponseCollection')->getMock();
        /**
         * @var AMQPMessage
         */
        //$message = $this->getMockBuilder('PhpAmqpLib\Message\AMQPMessage')->getMock();

        //$handler = new Handler();
        //$handler->handle($requests, $responses);
    }
}