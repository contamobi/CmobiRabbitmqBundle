<?php

namespace Cmobi\RabbitmqBundle\Queue;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;

interface QueueProducerInterface
{
    /** ttl in milliseconds */
    const DEFAULT_TTL = 30000;

    const PRIORITY_MAX = 100;
    const PRIORITY_MIDDLE = 50;
    const PRIORITY_LOW = 0;

    /**
     * @param $data
     * @param $expire
     * @param $priority
     * @return void
     */
    public function publish($data, $expire = self::DEFAULT_TTL, $priority = self::PRIORITY_LOW);

    /**
     * @return CmobiAMQPChannel
     */
    public function refreshChannel();

    /**
     * @return CmobiAMQPChannel
     */
    public function getChannel();

    /** @return string */
    public function generateCorrelationId();

    /** @return string */
    public function getCurrentCorrelationId();

    /**
     * @return string
     */
    public function getQueueName();

    /**
     * Return caller name
     *
     * @return string
     */
    public function getFromName();

    /**
     * @return string
     */
    public function getResponse();
}