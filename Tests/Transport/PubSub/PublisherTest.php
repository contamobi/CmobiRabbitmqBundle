<?php

namespace Cmobi\RabbitmqBundle\Tests\Transport\PubSub;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnection;
use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Queue\CmobiAMQPMessage;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use Cmobi\RabbitmqBundle\Transport\PubSub\ExchangeType;
use Cmobi\RabbitmqBundle\Transport\PubSub\Publisher;

class PublisherTest extends BaseTestCase
{
    public function testGetQueueName()
    {
        $publisher = new Publisher(
            'exch_test',
            ExchangeType::FANOUT,
            $this->getConnectionManagerMock(),
            'from_name_test',
            'test'
        );

        $this->assertEquals('test', $publisher->getQueueName());
    }

    public function testGetExchange()
    {
        $publisher = new Publisher(
            'exch_test',
            ExchangeType::FANOUT,
            $this->getConnectionManagerMock(),
            'test'
        );

        $this->assertEquals('exch_test', $publisher->getExchange());
    }

    public function testGetExchangeType()
    {
        $publisher = new Publisher(
            'exch_test',
            ExchangeType::DIRECT,
            $this->getConnectionManagerMock(),
            'test'
        );

        $this->assertEquals(ExchangeType::DIRECT, $publisher->getExchangeType());
    }

    public function testRefreshChannel()
    {
        $publisher = new Publisher(
            'exch_test',
            ExchangeType::FANOUT,
            $this->getConnectionManagerMock(),
            'test'
        );

        $this->assertInstanceOf(CmobiAMQPChannel::class, $publisher->refreshChannel());
    }

    public function testGetChannel()
    {
        $publisher = new Publisher(
            'exch_test',
            ExchangeType::FANOUT,
            $this->getConnectionManagerMock(),
            'test'
        );

        $this->assertInstanceOf(CmobiAMQPChannel::class, $publisher->refreshChannel());
    }

    public function testGetFromName()
    {
        $publisher = new Publisher(
            'exch_test',
            ExchangeType::FANOUT,
            $this->getConnectionManagerMock(),
            'caller_test',
            ''
        );

        $this->assertEquals('caller_test', $publisher->getFromName());
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