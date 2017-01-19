<?php

namespace Cmobi\RabbitmqBundle\Tests\Transport\Subscriber;

use Cmobi\RabbitmqBundle\Transport\Subscriber\ExchangeType;
use Cmobi\RabbitmqBundle\Transport\Subscriber\SubscriberQueueBag;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;

class SubscriberQueueBagTest extends BaseTestCase
{
    public function testGetQueue()
    {
        $queueBag = new SubscriberQueueBag('excharge1', ExchangeType::FANOUT, 'test');

        $this->assertEquals('test', $queueBag->getQueue());
    }

    public function testGetType()
    {
        $queueBag = new SubscriberQueueBag('test', ExchangeType::TOPIC);

        $this->assertEquals(ExchangeType::TOPIC, $queueBag->getType());
    }

    public function testGetPassive()
    {
        $queueBag = new SubscriberQueueBag('test');

        $this->assertEquals(false, $queueBag->getPassive());
    }

    public function testGetDurable()
    {
        $queueBag = new SubscriberQueueBag('test');

        $this->assertEquals(false, $queueBag->getDurable());
    }

    public function testGetAutoDelete()
    {
        $queueBag = new SubscriberQueueBag('test');

        $this->assertEquals(false, $queueBag->getAutoDelete());
    }

    public function testGetNoAck()
    {
        $queueBag = new SubscriberQueueBag('test');

        $this->assertEquals(true, $queueBag->getNoAck());
    }

    public function testGetNoLocal()
    {
        $queueBag = new SubscriberQueueBag('test');

        $this->assertEquals(false, $queueBag->getNoLocal());
    }

    public function testGetNoWait()
    {
        $queueBag = new SubscriberQueueBag('test');

        $this->assertEquals(false, $queueBag->getNoWait());
    }
    public function testGetArguments()
    {
        $queueBag = new SubscriberQueueBag('test');

        $this->assertEquals(null, $queueBag->getArguments());
    }
    public function testGetTicket()
    {
        $queueBag = new SubscriberQueueBag('test');

        $this->assertEquals(null, $queueBag->getTicket());
    }

    public function testGetQueueDeclare()
    {
        $queueBag = new SubscriberQueueBag('test');

        $this->assertEquals(
            [
                '',
                false,
                false,
                true,
                false,
                false,
                null,
                null,
            ],
            $queueBag->getQueueDeclare()
        );
    }

    public function testGetQueueConsume()
    {
        $queueBag = new SubscriberQueueBag('test');

        $this->assertEquals(
            [
                '',
                '',
                false,
                true,
                false,
                false,
                null,
                null,
            ],
            $queueBag->getQueueConsume()
        );
    }

    public function testGetExchangeDeclare()
    {
        $queueBag = new SubscriberQueueBag('test', ExchangeType::DIRECT);

        $this->assertEquals(
            [
                'test',
                ExchangeType::DIRECT,
                false,
                false,
                false,
                false,
                false,
                null,
                null,
            ],
            $queueBag->getExchangeDeclare()
        );
    }

    public function testBadTypeInGetExchangeDeclare()
    {
        $queueBag = new SubscriberQueueBag('test', ExchangeType::DIRECT);

        $this->assertNotEquals(
            [
                'test',
                ExchangeType::FANOUT,
                false,
                false,
                false,
                false,
                false,
                false,
                null,
                null,
            ],
            $queueBag->getExchangeDeclare()
        );
    }
}
