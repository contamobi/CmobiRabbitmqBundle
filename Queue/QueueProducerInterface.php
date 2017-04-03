<?php

namespace Cmobi\RabbitmqBundle\Queue;

interface QueueProducerInterface
{
    /** ttl in milliseconds */
    const DEFAULT_TTL = 15000;

    const PRIORITY_MAX = 100;
    const PRIORITY_MIDDLE = 50;
    const PRIORITY_LOW = 0;

    /**
     * @param $data
     * @param $expire
     * @param $priority
     */
    public function publish($data, $expire = self::DEFAULT_TTL, $priority = self::PRIORITY_LOW);

    /**
     * @return string
     */
    public function getQueueName();

    /**
     * @return string
     */
    public function getExchange();

    /**
     * @return string
     */
    public function getExchangeType();

    /**
     * Return caller name.
     *
     * @return string
     */
    public function getFromName();
}
