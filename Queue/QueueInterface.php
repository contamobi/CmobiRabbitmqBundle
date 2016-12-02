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
}