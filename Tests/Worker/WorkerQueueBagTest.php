<?php

namespace  Cmobi\RabbitmqBundle\Worker\Test\Worker;

use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use Cmobi\RabbitmqBundle\Worker\WorkerQueueBag;

class WorkerQueueBagTest extends BaseTestCase
{
    public function testGetBasicQos()
    {
        $queueBag = new WorkerQueueBag('test');

        $this->assertEquals(1, $queueBag->getBasicQos());
    }

    public function testGetQueueName()
    {
        $queueBag = new WorkerQueueBag('test');

        $this->assertEquals('test', $queueBag->getQueue());
    }

    public function testGetPassive()
    {
        $queueBag = new WorkerQueueBag('test');

        $this->assertEquals(false, $queueBag->getPassive());
    }
    public function testGetDurable()
    {
        $queueBag = new WorkerQueueBag('test');

        $this->assertEquals(true, $queueBag->getDurable());
    }
    public function testGetExclusive()
    {
        $queueBag = new WorkerQueueBag('test');

        $this->assertEquals(false, $queueBag->getExclusive());
    }
    public function testGetAutoDelete()
    {
        $queueBag = new WorkerQueueBag('test');

        $this->assertEquals(false, $queueBag->getAutoDelete());
    }
    public function testGetNoWait()
    {
        $queueBag = new WorkerQueueBag('test');

        $this->assertEquals(false, $queueBag->getNoWait());
    }
    public function testGetArguments()
    {
        $queueBag = new WorkerQueueBag('test');

        $this->assertEquals(null, $queueBag->getArguments());
    }
    public function testGetTicket()
    {
        $queueBag = new WorkerQueueBag('test');

        $this->assertEquals(null, $queueBag->getTicket());
    }

    public function testGetQueueDeclare()
    {
        $queueBag = new WorkerQueueBag('test');

        $this->assertEquals(
            [
                'test',
                false,
                true,
                false,
                false,
                false,
                null,
                null
            ],
            $queueBag->getQueueDeclare()
        );
    }

    public function testGetQueueConsume()
    {
        $queueBag = new WorkerQueueBag('test');

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
}