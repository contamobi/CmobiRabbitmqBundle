<?php

namespace Cmobi\RabbitmqBundle\Tests\Rpc;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnection;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnectionInterface;
use Cmobi\RabbitmqBundle\Queue\QueueInterface;
use Cmobi\RabbitmqBundle\Rpc\RpcServerBuilder;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use PhpAmqpLib\Channel\AMQPChannel;

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
        $queue = $rpcServer->buildQueue();

        $this->assertInstanceOf(QueueInterface::class, $queue);
    }

    public function testGetChannel()
    {
        $rpcServer = new RpcServerBuilder($this->getAMQPStreamConnectionMock(), []);
        $channel = $rpcServer->getChannel();

        $this->assertInstanceOf(AMQPChannel::class, $channel);
    }

    /**
     * @return CmobiAMQPConnectionInterface
     */
    protected function getAMQPStreamConnectionMock()
    {
        $class = $this->getMockBuilder(CmobiAMQPConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $channelMock =  $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $channelMock->method('basic_qos');

        $class->method('channel')
            ->willReturn($channelMock);

        return $class;
    }
}