<?php

namespace Cmobi\RabbitmqBundle\Queue;


interface QueueBuilderInterface
{
    /**
     * @param $queueName
     * @param QueueServiceInterface $queueService
     * @param QueueBagInterface $queueBag
     * @return Queue
     */
    public function buildQueue($queueName, QueueServiceInterface $queueService, QueueBagInterface $queueBag);
}
