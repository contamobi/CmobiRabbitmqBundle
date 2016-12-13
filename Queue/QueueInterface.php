<?php

namespace Cmobi\RabbitmqBundle\Queue;

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
     */
    public function forceReconnect();
}