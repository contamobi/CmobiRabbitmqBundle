<?php

namespace Cmobi\RabbitmqBundle\Tests\Handler;

use Cmobi\RabbitmqBundle\Rpc\Handler;
use Cmobi\RabbitmqBundle\Rpc\Request\JsonRpcRequestCollection;
use Cmobi\RabbitmqBundle\Rpc\Response\JsonRpcResponseCollection;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use PhpAmqpLib\Message\AMQPMessage;

class HandlerTest extends BaseTestCase
{
    public function testHandle()
    {
        /**
         * @var JsonRpcRequestCollection $requests
         */
        //$requests = $this->getMockBuilder('Cmobi\RabbitmqBundle\Rpc\Request\JsonRpcRequestCollection')->getMock();
        /**
         * @var JsonRpcResponseCollection $responses
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