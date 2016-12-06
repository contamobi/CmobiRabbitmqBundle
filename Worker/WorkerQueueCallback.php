<?php

namespace Cmobi\RabbitmqBundle\Worker;

use Cmobi\RabbitmqBundle\Queue\QueueCallbackInterface;
use Cmobi\RabbitmqBundle\Queue\QueueServiceInterface;
use PhpAmqpLib\Message\AMQPMessage;

class WorkerQueueCallback implements QueueCallbackInterface
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

            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
        };
    }
}