<?php

namespace Cmobi\RabbitmqBundle\Tests\Transport\Subscriber;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnection;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnectionInterface;
use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Queue\QueueInterface;
use Cmobi\RabbitmqBundle\Queue\QueueServiceInterface;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use Cmobi\RabbitmqBundle\Transport\Subscriber\SubscriberBuilder;
use Cmobi\RabbitmqBundle\Transport\Subscriber\SubscriberQueueBag;

class SubscriberBuilderTest extends BaseTestCase
{
    public function testGetConnectionManager()
    {
        $subscriberBuilder = new SubscriberBuilder(
            $this->getConnectionManagerMock(),
            $this->getLoggerMock(), []
        );
        $connectionManager = $subscriberBuilder->getConnectionManager();

        $this->assertInstanceOf(ConnectionManager::class, $connectionManager);
    }

    public function testBuildQueue()
    {
        $subscriberBuilder = new SubscriberBuilder(
            $this->getConnectionManagerMock(),
            $this->getLoggerMock()
        );
        $subscriberQueueBag = new SubscriberQueueBag('test_exchange');
        $queue = $subscriberBuilder->buildQueue('test', $this->getQueueServiceMock(), $subscriberQueueBag);

        $this->assertInstanceOf(QueueInterface::class, $queue);
    }

    /**
     * @return CmobiAMQPConnectionInterface
     */
    protected function getAMQPStreamConnectionMock()
    {
        $class = $this->getMockBuilder(CmobiAMQPConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $channelMock = $this->getMockBuilder(CmobiAMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $channelMock->expects($this->any())
            ->method('basic_qos')
            ->willReturn(true);

        $class->method('channel')
            ->willReturn($channelMock);

        return $class;
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
     * @return QueueServiceInterface
     */
    protected function getQueueServiceMock()
    {
        $queueCallback = $this->getMockBuilder(QueueServiceInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $queueCallback;
    }
}
