<?php

namespace Cmobi\RabbitmqBundle\Queue;

interface QueueBuilderInterface
{
    /**
     * @param $queueName
     *
     * @return QueueInterface
     */
    public function buildQueue($queueName);
}