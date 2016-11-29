<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\Queue\QueueBagInterface;

class RpcQueueBag implements QueueBagInterface
{
    private $options;

    public function __construct(
        $queueName,
        $basicQos = 1,
        $passive = false,
        $durable = false,
        $exclusive = false,
        $autoDelete = true,
        $noWait = false,
        array $arguments = null,
        $ticket = null,
        $consumerTag = '',
        $noAck = false,
        $noLocal = false
    )
    {
        $this->options = [
            'queue_name' => $queueName,
            'basic_qos' => $basicQos,
            'passive' => $passive,
            'durable' => $durable,
            'exclusive' => $exclusive,
            'auto_delete' => $autoDelete,
            'no_wait' => $noWait,
            'arguments' => $arguments,
            'ticket' => $ticket,
            'consumer_tag' => $consumerTag,
            'no_ack' => $noAck,
            'no_local' => $noLocal
        ];
    }

    /**
     * @return string
     */
    public function getQueueName()
    {
        return $this->options['queue_name'];
    }

    /**
     * @return int
     */
    public function getBasicQos()
    {
        return $this->options['basic_qos'];
    }

    /**
     * @return bool
     */
    public function getPassive()
    {
        return $this->options['passive'];
    }

    /**
     * @return bool
     */
    public function getDurable()
    {
        return $this->options['durable'];
    }

    /**
     * @return bool
     */
    public function getExclusive()
    {
        return $this->options['exclusive'];
    }

    /**
     * @return bool
     */
    public function getAutoDelete()
    {
        return $this->options['auto_delete'];
    }

    /**
     * @return bool
     */
    public function getNoWait()
    {
        return $this->options['no_wait'];
    }

    /**
     * @return string
     */
    public function getArguments()
    {
        return $this->options['arguments'];
    }

    /**
     * @return string
     */
    public function getTicket()
    {
        return $this->options['ticket'];
    }

    /**
     * @return string
     */
    public function getConsumerTag()
    {
        return $this->options['consumer_tag'];
    }

    /**
     * @return bool
     */
    public function getNoAck()
    {
        return $this->options['no_ack'];
    }

    /**
     * @return bool
     */
    public function getNoLocal()
    {
        return $this->options['no_local'];
    }

    /**
     * @return array
     */
    public function getQueueDeclare()
    {
        return [
            $this->getQueueName(),
            $this->getPassive(),
            $this->getDurable(),
            $this->getExclusive(),
            $this->getAutoDelete(),
            $this->getNoWait(),
            $this->getArguments(),
            $this->getTicket()
        ];
    }

    /**
     * @return array
     */
    public function getQueueConsume()
    {
        return [
            $this->getQueueName(),
            $this->getConsumerTag(),
            $this->getConsumerTag(),
            $this->getNoAck(),
            $this->getExclusive(),
            $this->getNoWait(),
            $this->getTicket(),
            $this->getArguments()
        ];
    }
}