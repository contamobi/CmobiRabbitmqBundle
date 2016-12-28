<?php

namespace Cmobi\RabbitmqBundle\Connection;

interface CmobiAMQPConnectionInterface
{
    /**
     * @return CmobiAMQPChannel
     */
    public function channel();

    public function isConnected();

    public function reconnect();

    public function close();
}
