<?php

namespace Cmobi\RabbitmqBundle\Test\Queue;

use Cmobi\RabbitmqBundle\Queue\CmobiAMQPMessage;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use PhpAmqpLib\Message\AMQPMessage;

class CmobiAMQPMessageTest extends BaseTestCase
{
    public function testType()
    {
        $message = new CmobiAMQPMessage();

        $this->assertInstanceOf(AMQPMessage::class, $message);
    }
}
