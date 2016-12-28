<?php

namespace Cmobi\RabbitmqBundle\Tests\Queue;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Queue\Queue;
use Cmobi\RabbitmqBundle\Queue\QueueBagInterface;
use Cmobi\RabbitmqBundle\Queue\QueueCallbackInterface;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;

class QueueTest extends BaseTestCase
{
    public function testGetQueueBag()
    {
        $queue = new Queue($this->getConnectionManagerMock(), $this->getQueueBagMock(), $this->getLoggerMock());

        $this->assertInstanceOf(QueueBagInterface::class, $queue->getQueuebag());
    }

    public function testGetCallback()
    {
        $queue = new Queue($this->getConnectionManagerMock(), $this->getQueueBagMock(), $this->getLoggerMock());
        $queue->setCallback($this->getQueueCallbackMock());

        $callback = $queue->getCallback();

        $this->assertInstanceOf(QueueCallbackInterface::class, $callback);
    }

    /**
     * @return QueueBagInterface
     */
    protected function getQueueBagMock()
    {
        $bagMock = $this->getMockBuilder(QueueBagInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $bagMock;
    }

    /**
     * @return CmobiAMQPChannel
     */
    protected function getCmobiAMQPChannelMock()
    {
        $channelMock = $this->getMockBuilder(CmobiAMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $channelMock->expects($this->any())
            ->method('basic_qos')
            ->willReturn(true);

        return $channelMock;
    }

    /**
     * @return ConnectionManager
     */
    protected function getConnectionManagerMock()
    {
        $connectionManagerMock = $this->getMockBuilder(ConnectionManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $connectionManagerMock;
    }

    /**
     * @return QueueCallbackInterface
     */
    protected function getQueueCallbackMock()
    {
        $callbackMock = $this->getMockBuilder(QueueCallbackInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $callbackMock->method('toClosure')
            ->willReturn(function () {});

        return $callbackMock;
    }
}
