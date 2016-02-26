<?php

namespace Cmobi\RabbitmqBundle\Amqp;

use Cmobi\RabbitmqBundle\Amqp\Exception\InvalidAMQPConnectionClassException;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;

class ConnectionFactoryTest extends BaseTestCase
{
    public  function testInvalidClassException()
    {
        $this->setExpectedException(InvalidAMQPConnectionClassException::class);
        $class = 'GenericNotAmqpInstanceStreamConnection';
        new ConnectionFactory($class, []);
    }
}