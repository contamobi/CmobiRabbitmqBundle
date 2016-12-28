<?php

namespace Cmobi\RabbitmqBundle\Queue;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnection;

interface QueueInterface
{
    /**
     * Declare and start queue in broker
     */
    public function start();

    /**
     * @return QueueBagInterface
     */
    public function getQueueBag();

    /**
     * @return QueueCallbackInterface
     */
    public function getCallback();

    /**
     * Retry connect to message broker until it can.
     *
     * @param CmobiAMQPConnection|null $connection
     */
    public function forceReconnect(CmobiAMQPConnection $connection = null);
}