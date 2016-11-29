<?php

namespace Cmobi\RabbitmqBundle\Queue;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use PhpAmqpLib\Exception\AMQPRuntimeException;

class Queue implements QueueInterface
{
    private $channel;
    private $queueBag;
    private $callback;

    public function __construct(CmobiAMQPChannel $channel, QueueBagInterface $queueBag)
    {
        $this->channel = $channel;
        $this->queueBag = $queueBag;
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

        $this->getChannel()->basicConsume($queueBag->getQueueConsume(), $this->getCallback());

        while(count($this->getChannel()->callbacks)) {
            try {
                $this->getChannel()->wait();
            } catch (AMQPRuntimeException $e) {

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
     * @param \Closure $function
     */
    public function setCallback(\Closure $function)
    {
        $this->callback = $function;
    }

    /**
     * @return \Closure
     */
    public function getCallback()
    {
        return $this->callback;
    }
}