<?php

namespace Cmobi\RabbitmqBundle\Amqp;

use PhpAmqpLib\Connection\AMQPStreamConnection;

interface ConnectionFactoryInterface
{
    /**
     * @return AMQPStreamConnection
     */
    public function createConnection();
}