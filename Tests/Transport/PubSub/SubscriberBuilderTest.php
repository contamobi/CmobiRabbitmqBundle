<?php

namespace Cmobi\RabbitmqBundle\Tests\Transport\PubSub;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnection;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnectionInterface;
use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Queue\QueueInterface;
use Cmobi\RabbitmqBundle\Queue\QueueServiceInterface;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use Cmobi\RabbitmqBundle\Transport\PubSub\ExchangeType;
use Cmobi\RabbitmqBundle\Transport\PubSub\SubscriberBuilder;

class SubscriberBuilderTest extends BaseTestCase
{
    public function testGetConnectionManager()
    {
        $subscriberBuilder = new SubscriberBuilder(
            'test',
            ExchangeType::FANOUT,
            $this->getConnectionManagerMock(),
            $this->getLoggerMock(), []
        );
        $connectionManager = $subscriberBuilder->getConnectionManager();

        $this->assertInstanceOf(ConnectionManager::class, $connectionManager);
    }

    public function testBuildQueue()
    {
        $subscriberBuilder = new SubscriberBuilder(
            'test',
            ExchangeType::FANOUT,
            $this->getConnectionManagerMock(),
            $this->getLoggerMock(),
            []
        );
        $queue = $subscriberBuilder->buildQueue('test', $this->getQueueServiceMock());

        $this->assertInstanceOf(QueueInterface::class, $queue);
    }

    public function testGetExchange()
    {
        $subscriberBuilder = new SubscriberBuilder(
            'test_exchange',
             ExchangeType::FANOUT,
            $this->getConnectionManagerMock(),
            $this->getLoggerMock(),
            []
        );

        $this->assertEquals('test_exchange', $subscriberBuilder->getExchangeName());
    }

    public function testGetExchangeType()
    {
        $subscriberBuilder = new SubscriberBuilder(
            'test_exchange',
            ExchangeType::DIRECT,
            $this->getConnectionManagerMock(),
            $this->getLoggerMock(),
            []
        );

        $this->assertEquals(ExchangeType::DIRECT, $subscriberBuilder->getExchangeType());
    }

    /**
     * @return CmobiAMQPConnectionInterface
     */
    protected function getAMQPStreamConnectionMock()
    {
        $class = $this->getMockBuilder(CmobiAMQPConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $channelMock =  $this->getMockBuilder(CmobiAMQPChannel::class)
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