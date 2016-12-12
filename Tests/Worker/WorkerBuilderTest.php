<?php

namespace Cmobi\RabbitmqBundle\Tests\Rpc;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnection;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnectionInterface;
use Cmobi\RabbitmqBundle\Queue\QueueCallbackInterface;
use Cmobi\RabbitmqBundle\Queue\QueueInterface;
use Cmobi\RabbitmqBundle\Queue\QueueServiceInterface;
use Cmobi\RabbitmqBundle\Rpc\RpcServerBuilder;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use Cmobi\RabbitmqBundle\Worker\WorkerBuilder;

class WorkerBuilderTest extends BaseTestCase
{
    public function testGetConnection()
    {
        $workerServer = new WorkerBuilder($this->getAMQPStreamConnectionMock(), $this->getLoggerMock(), []);
        $connection = $workerServer->getConnection();

        $this->assertInstanceOf(CmobiAMQPConnectionInterface::class, $connection);
    }

    public function testBuildQueue()
    {
        $rpcServer = new RpcServerBuilder($this->getAMQPStreamConnectionMock(), $this->getLoggerMock(), []);
        $queue = $rpcServer->buildQueue('test', $this->getQueueServiceMock());

        $this->assertInstanceOf(QueueInterface::class, $queue);
    }

    public function testGetCallbackAfterBuildQueue()
    {
        $rpcServer = new RpcServerBuilder($this->getAMQPStreamConnectionMock(), $this->getLoggerMock(), []);
        $queue = $rpcServer->buildQueue('test', $this->getQueueServiceMock());
        $callback = $queue->getCallback();

        $this->assertInstanceOf(QueueCallbackInterface::class, $callback);
    }

    public function testGetChannel()
    {
        $rpcServer = new RpcServerBuilder($this->getAMQPStreamConnectionMock(), $this->getLoggerMock(), []);
        $channel = $rpcServer->getChannel();

        $this->assertInstanceOf(CmobiAMQPChannel::class, $channel);
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