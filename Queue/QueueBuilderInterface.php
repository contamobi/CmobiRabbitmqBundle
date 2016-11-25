<?php

namespace Cmobi\RabbitmqBundle\Queue;

interface QueueBuilderInterface
{
    /**
     * @return QueueInterface
     */
    public function buildQueue();
}