<?php

namespace Cmobi\RabbitmqBundle\Tests\Connection;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnection;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnectionInterface;
use Cmobi\RabbitmqBundle\Connection\ConnectionFactory;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use PhpAmqpLib\Connection\AMQPStreamConnection;


class ConnectionFactoryTest extends BaseTestCase
{
    public function testAMQPConnectionParameter()
    {
        $class = $this->getContainer()->getParameter('cmobi_rabbitmq.connection.class');

        $this->assertTrue(is_a($class, CmobiAMQPConnectionInterface::class, true));
    }

    public function testAMQPConnectionFactory()
    {
        $factory = new ConnectionFactory($this->getAMQPStreamConnectionMock(), []);
        $connection = $factory->createConnection();

        $this->assertInstanceOf(CmobiAMQPConnectionInterface::class, $connection);
    }

    protected function getAMQPStreamConnectionMock()
    {
        $class = $this->getMockBuilder(CmobiAMQPConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $class;
    }
}