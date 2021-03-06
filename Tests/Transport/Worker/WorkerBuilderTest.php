<?php

namespace Cmobi\RabbitmqBundle\Tests\Transport\Rpc;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnection;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnectionInterface;
use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Queue\QueueInterface;
use Cmobi\RabbitmqBundle\Queue\QueueServiceInterface;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use Cmobi\RabbitmqBundle\Transport\Worker\WorkerBuilder;
use Cmobi\RabbitmqBundle\Transport\Worker\WorkerQueueBag;

class WorkerBuilderTest extends BaseTestCase
{
    public function testGetConnectionManager()
    {
        $workerServer = new WorkerBuilder($this->getConnectionManagerMock(), $this->getLoggerMock(), []);
        $connectionManager = $workerServer->getConnectionManager();

        $this->assertInstanceOf(ConnectionManager::class, $connectionManager);
    }

    public function testBuildQueue()
    {
        $workerServer = new WorkerBuilder($this->getConnectionManagerMock(), $this->getLoggerMock(), []);
        $workerBag = new WorkerQueueBag('test_queue');
        $queue = $workerServer->buildQueue('test', $this->getQueueServiceMock(), $workerBag);

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
