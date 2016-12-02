<?php

namespace Cmobi\RabbitmqBundle\Queue;

interface QueueServiceInterface
{
    /**
     * @param CmobiAMQPMessage $message
     * @return string
     */
    public function handle(CmobiAMQPMessage $message);
}