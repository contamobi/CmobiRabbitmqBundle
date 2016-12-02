<?php

namespace Cmobi\RabbitmqBundle\Queue;

interface QueueBuilderInterface
{
    /**
     * @param $queueName
     * @param QueueServiceInterface $queueService
     *
     * @return QueueInterface
     */
    public function buildQueue($queueName, QueueServiceInterface $queueService);
}