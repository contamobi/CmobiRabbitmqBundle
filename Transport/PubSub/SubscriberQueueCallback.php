<?php

namespace Cmobi\RabbitmqBundle\Transport\PubSub;

use Cmobi\RabbitmqBundle\Queue\QueueCallbackInterface;
use Cmobi\RabbitmqBundle\Queue\QueueServiceInterface;
use PhpAmqpLib\Message\AMQPMessage;

class SubscriberQueueCallback implements QueueCallbackInterface
{
    private $queueService;

    public function __construct(QueueServiceInterface $queueService)
    {
        $this->queueService = $queueService;
    }

    /**
     * @return QueueServiceInterface
     */
    public function getQueueService()
    {
        return $this->queueService;
    }

    /**
     * @return \Closure
     */
    public function toClosure()
    {
        return function (AMQPMessage $message) {
            $this->getQueueService()->handle($message);
        };
    }
}