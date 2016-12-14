<?php

namespace Cmobi\RabbitmqBundle\Tests\Transport\Worker;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnection;
use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Queue\CmobiAMQPMessage;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use Cmobi\RabbitmqBundle\Transport\Worker\Task;

class TaskTest extends BaseTestCase
{
    public function testGetQueueName()
    {
        $taskClient = new Task('test', $this->getConnectionManagerMock());

        $this->assertEquals('test', $taskClient->getQueueName());
    }

    public function testRefreshChannel()
    {
        $taskClient = new Task('test', $this->getConnectionManagerMock());

        $this->assertInstanceOf(CmobiAMQPChannel::class, $taskClient->refreshChannel());
    }

    public function testGetChannel()
    {
        $taskClient = new Task('test', $this->getConnectionManagerMock());

        $this->assertInstanceOf(CmobiAMQPChannel::class, $taskClient->refreshChannel());
    }

    public function testGetFromName()
    {
        $taskClient = new Task('test', $this->getConnectionManagerMock(), 'caller_test');

        $this->assertEquals('caller_test', $taskClient->getFromName());
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