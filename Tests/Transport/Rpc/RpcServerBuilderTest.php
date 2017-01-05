<?php

namespace Cmobi\RabbitmqBundle\Tests\Rpc;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnection;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnectionInterface;
use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Queue\QueueCallbackInterface;
use Cmobi\RabbitmqBundle\Queue\QueueInterface;
use Cmobi\RabbitmqBundle\Queue\QueueServiceInterface;
use Cmobi\RabbitmqBundle\Transport\Rpc\RpcQueueBag;
use Cmobi\RabbitmqBundle\Transport\Rpc\RpcServerBuilder;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;

class RpcServerBuilderTest extends BaseTestCase
{
    public function testGetConnectionManager()
    {
        $rpcServer = new RpcServerBuilder($this->getConnectionManagerMock(), $this->getLoggerMock(), []);
        $connectionManager = $rpcServer->getConnectionManager();

        $this->assertInstanceOf(ConnectionManager::class, $connectionManager);
    }

    public function testBuildQueue()
    {
        $rpcServer = new RpcServerBuilder($this->getConnectionManagerMock(), $this->getLoggerMock(), []);
        $rpcServerQueueBag = new RpcQueueBag('test_queue');
        $queue = $rpcServer->buildQueue('test', $this->getQueueServiceMock(), $rpcServerQueueBag);

        $this->assertInstanceOf(QueueInterface::class, $queue);
    }

    public function testGetCallbackAfterBuildQueue()
    {
        $rpcServer = new RpcServerBuilder($this->getConnectionManagerMock(), $this->getLoggerMock(), []);
        $rpcServerQueueBag = new RpcQueueBag('test_queue');
        $queue = $rpcServer->buildQueue('test', $this->getQueueServiceMock(), $rpcServerQueueBag);
        $callback = $queue->getCallback();

        $this->assertInstanceOf(QueueCallbackInterface::class, $callback);
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
