<?php

namespace Cmobi\RabbitmqBundle\Queue;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use Psr\Log\LoggerInterface;

class Queue implements QueueInterface
{
    private $channel;
    private $queueBag;
    private $callback;
    private $logger;

    public function __construct(CmobiAMQPChannel $channel, QueueBagInterface $queueBag, LoggerInterface $logger)
    {
        $this->channel = $channel;
        $this->queueBag = $queueBag;
        $this->logger = $logger;
    }

    /**
     * @return CmobiAMQPChannel
     */
    protected function getChannel()
    {
        return $this->channel;
    }

    /**
     * Declare and start queue in broker
     */
    public function start()
    {
        $queueBag = $this->getQueuebag();
        $this->getChannel()->basic_qos(null, $queueBag->getBasicQos(), null);
        $this->getChannel()->queueDeclare($queueBag->getQueueDeclare());

        $this->getChannel()->basicConsume($queueBag->getQueueConsume(), $this->getCallback()->toClosure());

        while(count($this->getChannel()->callbacks)) {
            try {
                $this->getChannel()->wait();
            } catch (AMQPRuntimeException $e) {
                $this->logger->error($e->getMessage());

                continue;
            }
        }
        $connection = $this->getChannel()->getConnection();
        $this->getChannel()->close();
        $connection->close();
    }

    /**
     * @return QueueBagInterface
     */
    public function getQueuebag()
    {
        return $this->queueBag;
    }

    /**
     * @param QueueCallbackInterface $callback
     */
    public function setCallback(QueueCallbackInterface $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return QueueCallbackInterface
     */
    public function getCallback()
    {
        return $this->callback;
    }
}