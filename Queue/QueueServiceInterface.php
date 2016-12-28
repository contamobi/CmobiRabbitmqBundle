<?php

namespace Cmobi\RabbitmqBundle\Queue;

use PhpAmqpLib\Message\AMQPMessage;

interface QueueServiceInterface
{
    /**
     * @param AMQPMessage $message
     *
     * @return string
     */
    public function handle(AMQPMessage $message);
}
