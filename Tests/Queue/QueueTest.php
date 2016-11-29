<?php

namespace Cmobi\RabbitmqBundle\Tests\Queue;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Queue\Queue;
use Cmobi\RabbitmqBundle\Queue\QueueBagInterface;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use ReflectionFunction;

class QueueTest extends BaseTestCase
{
    public function testGetQueueBag()
    {
        $queue = new Queue($this->getCmobiAMQPChannelMock(), $this->getQueueBagMock());

        $this->assertInstanceOf(QueueBagInterface::class, $queue->getQueuebag());
    }

    public function testGetCallback()
    {
        $queue = new Queue($this->getCmobiAMQPChannelMock(), $this->getQueueBagMock());
        $queue->setCallback(function($a) { return $a; });

        $func = new ReflectionFunction($queue->getCallback());

        $this->assertTrue($func->isClosure());
    }

    /**
     * @return QueueBagInterface
     */
    public function getQueueBagMock()
    {
        $bagMock = $this->getMockBuilder(QueueBagInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $bagMock;
    }

    /**
     * @return CmobiAMQPChannel
     */
    public function getCmobiAMQPChannelMock()
    {
        $channelMock =  $this->getMockBuilder(CmobiAMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $channelMock->expects($this->any())
            ->method('basic_qos')
            ->willReturn(true);

        return $channelMock;
    }
}