<?php

namespace Cmobi\RabbitmqBundle\Tests\Rpc;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnection;
use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Rpc\RpcClient;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;

class RpcClientTest extends BaseTestCase
{
    private $responseFromBasicConsumeInChannel;

    public function testGetQueueName()
    {
        $rpcClient = new RpcClient('test', $this->getConnectionManagerMock());

        $this->assertEquals('test', $rpcClient->getQueueName());
    }

    public function testRefreshChannel()
    {
        $rpcClient = new RpcClient('test', $this->getConnectionManagerMock());

        $this->assertInstanceOf(CmobiAMQPChannel::class, $rpcClient->refreshChannel());
    }

    public function testGetChannel()
    {
        $rpcClient = new RpcClient('test', $this->getConnectionManagerMock());

        $this->assertInstanceOf(CmobiAMQPChannel::class, $rpcClient->refreshChannel());
    }

    public function testGetFromName()
    {
        $rpcClient = new RpcClient('test', $this->getConnectionManagerMock(), 'caller_test');

        $this->assertEquals('caller_test', $rpcClient->getFromName());
    }

    public function testGetResponse()
    {
        $rpcClient = new RpcClient('test', $this->getConnectionManagerMock(), 'caller_test');
        $this->responseFromBasicConsumeInChannel = 'testGetResponse() - OK';
        $rpcClient->publish('test');

        $this->assertEquals('testGetResponse() - OK', $rpcClient->getResponse());
    }

    /**
     * @return ConnectionManager
     */
    protected function getConnectionManagerMock()
    {
        $connectionManagerMock = $this->getMockBuilder(ConnectionManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $connectionManagerMock->method('getConnection')
            ->willReturn($this->getConnectionMock());

        return $connectionManagerMock;
    }

    /**
     * @return CmobiAMQPConnection
     */
    protected function getConnectionMock()
    {
        $connectionMock = $this->getMockBuilder(CmobiAMQPConnection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $connectionMock->method('isConnected')
            ->willReturn(true);
        $connectionMock->method('reconnect')
            ->willReturn(true);
        $connectionMock->method('channel')
            ->willReturn($this->getChannelMock());

        return $connectionMock;
    }

    /**
     * @return CmobiAMQPChannel
     */
    protected function getChannelMock()
    {
        $basicConsumeResponse = $this->responseFromBasicConsumeInChannel;

        $channelMock = $this->getMockBuilder(CmobiAMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $channelMock
            ->expects($this->any())
            ->method('basic_publish')
            ->willReturn(true);
        $channelMock
            ->method('basicConsume')
            ->with($this->returnCallback(function($params, $callback) use ($basicConsumeResponse) {

                //$callback[0]->$callback[1]($basicConsumeResponse);
            }))
            ->willReturn(true);


        return $channelMock;
    }
}