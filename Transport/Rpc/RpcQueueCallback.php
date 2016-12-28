<?php

namespace Cmobi\RabbitmqBundle\Transport\Rpc;

use Cmobi\RabbitmqBundle\Queue\CmobiAMQPMessage;
use Cmobi\RabbitmqBundle\Queue\QueueCallbackInterface;
use Cmobi\RabbitmqBundle\Queue\QueueServiceInterface;
use PhpAmqpLib\Message\AMQPMessage;

class RpcQueueCallback implements QueueCallbackInterface
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
            $response = $this->getQueueService()->handle($message);

            $amqpResponse = new CmobiAMQPMessage(
                (string) $response,
                ['correlation_id' => $message->get('correlation_id')]
            );

            $message->delivery_info['channel']->basic_publish(
                $amqpResponse,
                '',
                $message->get('reply_to')
            );
            $message->delivery_info['channel']->basic_ack(
                $message->delivery_info['delivery_tag']
            );
        };
    }
}
