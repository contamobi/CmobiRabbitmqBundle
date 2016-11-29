<?php

namespace Cmobi\RabbitmqBundle\Tests\Rpc;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnection;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnectionInterface;
use Cmobi\RabbitmqBundle\Queue\QueueInterface;
use Cmobi\RabbitmqBundle\Rpc\RpcServerBuilder;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;

class RpcServerBuilderTest extends BaseTestCase
{
    public function testGetConnection()
    {
        $rpcServer = new RpcServerBuilder($this->getAMQPStreamConnectionMock(), []);
        $connection = $rpcServer->getConnection();

        $this->assertInstanceOf(CmobiAMQPConnectionInterface::class, $connection);
    }

    public function testBuildQueue()
    {
        $rpcServer = new RpcServerBuilder($this->getAMQPStreamConnectionMock(),[]);
        $queue = $rpcServer->buildQueue('test');

        $this->assertInstanceOf(QueueInterface::class, $queue);
    }

    public function testGetChannel()
    {
        $rpcServer = new RpcServerBuilder($this->getAMQPStreamConnectionMock(), []);
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
}