<?php

namespace Cmobi\RabbitmqBundle\Queue;

use PhpAmqpLib\Message\AMQPMessage;

class BaseQueueService implements QueueServiceInterface
{
    /**
     * @param AMQPMessage $message
     *
     * @return string
     */
    public function handle(AMQPMessage $message)
    {
        // TODO: Implement handle() method.
    }
}