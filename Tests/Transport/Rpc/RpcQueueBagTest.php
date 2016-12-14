<?php

namespace Cmobi\RabbitmqBundle\Tests\Rpc;

use Cmobi\RabbitmqBundle\Transport\Rpc\RpcQueueBag;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;

class RpcQueueBagTest extends BaseTestCase
{
    public function testGetBasicQos()
    {
        $queueBag = new RpcQueueBag('test');

        $this->assertEquals(1, $queueBag->getBasicQos());
    }

    public function testGetQueueName()
    {
        $queueBag = new RpcQueueBag('test');

        $this->assertEquals('test', $queueBag->getQueue());
    }

    public function testGetPassive()
    {
        $queueBag = new RpcQueueBag('test');

        $this->assertEquals(false, $queueBag->getPassive());
    }

    public function testGetDurable()
    {
        $queueBag = new RpcQueueBag('test');

        $this->assertEquals(false, $queueBag->getDurable());
    }

    public function testGetExclusive()
    {
        $queueBag = new RpcQueueBag('test');

        $this->assertEquals(false, $queueBag->getExclusive());
    }

    public function testGetAutoDelete()
    {
        $queueBag = new RpcQueueBag('test');

        $this->assertEquals(true, $queueBag->getAutoDelete());
    }

    public function testGetNoWait()
    {
        $queueBag = new RpcQueueBag('test');

        $this->assertEquals(false, $queueBag->getNoWait());
    }

    public function testGetArguments()
    {
        $queueBag = new RpcQueueBag('test');

        $this->assertEquals(null, $queueBag->getArguments());
    }

    public function testGetTicket()
    {
        $queueBag = new RpcQueueBag('test');

        $this->assertEquals(null, $queueBag->getTicket());
    }

    public function testGetQueueDeclare()
    {
        $queueBag = new RpcQueueBag('test');

        $this->assertEquals(
            [
                'test',
                false,
                false,
                false,
                true,
                false,
                null,
                null
            ],
            $queueBag->getQueueDeclare()
        );
    }

    public function testGetQueueConsume()
    {
        $queueBag = new RpcQueueBag('test');

        $this->assertEquals(
            [
                'test',
                '',
                false,
                false,
                false,
                false,
                null,
                null
            ],
            $queueBag->getQueueConsume()
        );
    }

    public function testGetExchangeDeclare()
    {
        $queueBag = new RpcQueueBag('test');

        $this->assertEquals(false, $queueBag->getExchangeDeclare());
    }
}