<?php

namespace Cmobi\RabbitmqBundle\Queue;

interface QueueBuilderInterface
{
    /**
     * @return string|false
     */
    public function getExchangeName();

    /**
     * @return string|false
     */
    public function getExchangeType();

    /**
     * @param $queueName
     * @param QueueServiceInterface $queueService
     *
     * @return QueueInterface
     */
    public function buildQueue($queueName, QueueServiceInterface $queueService);
}
