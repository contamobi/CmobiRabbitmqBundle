<?php

namespace Cmobi\RabbitmqBundle;

use PhpAmqpLib\Connection\AMQPStreamConnection;

interface ConnectionManagerInterface
{
    /**
     * @param null $name
     * @return AMQPStreamConnection
     * @throws \Exception
     */
    public function getConnection($name = null);
}