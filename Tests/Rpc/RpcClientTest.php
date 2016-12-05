<?php

namespace Cmobi\RabbitmqBundle\Tests\Rpc;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnection;
use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Queue\CmobiAMQPMessage;
use Cmobi\RabbitmqBundle\Rpc\RpcClient;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;

class RpcClientTest extends BaseTestCase
{
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

        /** @Todo prevent infinite while - improve it */
        $rpcClient->setResponse('testGetResponse() - OK');

        $rpcClient->publish('test');
        $rpcClient->onResponse(
            $this->getCmobiAMQPMessage('testGetResponse() - OK',
                $rpcClient->getCurrentCorrelationId())
        );

        $this->assertEquals('testGetResponse() - OK', $rpcClient->getResponse());
    }

    public function testGenerateCorrelationId()
    {
        $rpcClient = new RpcClient('test', $this->getConnectionManagerMock(), 'caller_test');

        $this->assertRegExp(sprintf('/%s/', $rpcClient->getCurrentCorrelationId()), $rpcClient->getQueueName());
    }

    public function testGetCurrentCorrelationId()
    {
        $rpcClient = new RpcClient('test', $this->getConnectionManagerMock(), 'caller_test');
        /** @Todo prevent infinite while - improve it */
        $rpcClient->setResponse('testGetResponse() - OK');

        $rpcClient->publish('test');
        $rpcClient->onResponse(
            $this->getCmobiAMQPMessage('testGetResponse() - OK',
                $rpcClient->getCurrentCorrelationId())
        );
        $this->assertNotNull($rpcClient->getCurrentCorrelationId());
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
        $channelMock = $this->getMockBuilder(CmobiAMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $channelMock
            ->expects($this->any())
            ->method('basic_publish')
            ->willReturn(true);
        $channelMock
            ->method('basicConsume')
            ->willReturn(true);


        return $channelMock;
    }

    /**
     * @param string $msg
     * @param null $correlationId
     * @return CmobiAMQPMessage
     */
    protected function getCmobiAMQPMessage($msg = '', $correlationId = null)
    {
        $msgMock = $this->getMockBuilder(CmobiAMQPMessage::class)
            ->disableOriginalConstructor()
            ->getMock();
        $msgMock->method('get')
            ->willReturn('correlation_id')
            ->willReturn($correlationId);
        $msgMock->method('getBody')
            ->willReturn($msg);

        return $msgMock;
    }
}