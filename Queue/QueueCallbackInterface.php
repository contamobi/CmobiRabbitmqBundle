<?php

namespace Cmobi\RabbitmqBundle\Queue;

interface QueueCallbackInterface
{
    /**
     * @return QueueServiceInterface
     */
    public function getQueueService();

    /**
     * @return \Closure
     */
    public function toClosure();
}
