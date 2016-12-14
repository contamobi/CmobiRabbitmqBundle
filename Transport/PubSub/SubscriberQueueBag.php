<?php

namespace Cmobi\RabbitmqBundle\Transport\PubSub;

use Cmobi\RabbitmqBundle\Queue\QueueBagInterface;

class SubscriberQueueBag implements QueueBagInterface
{
    private $options;

    public function __construct(
        $exchange,
        $type = ExchangeType::FANOUT,
        $queueName = null,
        $basicQos = 1,
        $passive = false,
        $durable = false,
        $declareExclusive = true,
        $consumeExclusive = false,
        $internal = false,
        $autoDelete = false,
        $noWait = false,
        array $arguments = null,
        $ticket = null,
        $consumerTag = '',
        $noAck = true,
        $noLocal = false
    )
    {
        $this->options = [
            'exchange' => $exchange,
            'type' => $type,
            'basic_qos' => $basicQos,
            'queue_name' => $queueName,
            'passive' => $passive,
            'durable' => $durable,
            'declare_exclusive' => $declareExclusive,
            'consume_exclusive' => $consumeExclusive,
            'internal' => $internal,
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
     * @param $exchange
     */
    public function setExchange($exchange)
    {
        $this->options['exchange'] = $exchange;
    }

    /**
     * @return string|mixed
     */
    public function getExchange()
    {
        return $this->options['exchange'];
    }

    /**
     * @param $qos
     */
    public function setBasicQos($qos)
    {
        $this->options['basic_qos'] = $qos;
    }

    /**
     * @return int
     */
    public function getBasicQos()
    {
        return $this->options['basic_qos'];
    }

    /**
     * @param $queueName
     */
    public function setQueueName($queueName)
    {
        $this->options['queue_name'] = $queueName;
    }

    /**
     * @return string
     */
    public function getQueueName()
    {
        return $this->options['queue_name'];
    }

    /**
     * @param $type
     */
    public function setType($type)
    {
        $this->options['type'] = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->options['type'];
    }

    /**
     * @param $passive
     */
    public function setPassive($passive)
    {
        $this->options['passive'] = $passive;
    }

    /**
     * @return bool
     */
    public function getPassive()
    {
        return $this->options['passive'];
    }

    /**
     * @param $internal
     */
    public function setInternal($internal)
    {
        $this->options['internal'] = $internal;
    }

    /**
     * @return bool
     */
    public function getInternal()
    {
        return $this->options['internal'];
    }

    /**
     * @param $durable
     */
    public function setDurable($durable)
    {
        $this->options['durable'] = $durable;
    }

    /**
     * @return bool
     */
    public function getDurable()
    {
        return $this->options['durable'];
    }

    /**
     * @param $exclusive
     */
    public function setDeclareExclusive($exclusive)
    {
        $this->options['declare_exclusive'] = $exclusive;
    }

    /**
     * @return bool
     */
    public function getDeclareExclusive()
    {
        return $this->options['declare_exclusive'];
    }

    /**
     * @param $exclusive
     */
    public function setConsumeExclusive($exclusive)
    {
        $this->options['consume_exclusive'] = $exclusive;
    }

    /**
     * @return bool
     */
    public function getConsumeExclusive()
    {
        return $this->options['consume_exclusive'];
    }

    /**
     * @param $autoDelete
     */
    public function setAutoDelete($autoDelete)
    {
        $this->options['auto_delete'] = $autoDelete;
    }

    /**
     * @return bool
     */
    public function getAutoDelete()
    {
        return $this->options['auto_delete'];
    }

    /**
     * @param $noWait
     */
    public function setNoWait($noWait)
    {
        $this->options['no_wait'] = $noWait;
    }

    /**
     * @return bool
     */
    public function getNoWait()
    {
        return $this->options['no_wait'];
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments)
    {
        $this->options['arguments'] = $arguments;
    }

    /**
     * @return string
     */
    public function getArguments()
    {
        return $this->options['arguments'];
    }

    /**
     * @param $ticket
     */
    public function setTicket($ticket)
    {
        $this->options['ticket'] = $ticket;
    }

    /**
     * @return string
     */
    public function getTicket()
    {
        return $this->options['ticket'];
    }

    /**
     * @param $consumerTag
     */
    public function setConsumerTag($consumerTag)
    {
        $this->options['consumer_tag'] = $consumerTag;
    }

    /**
     * @return string
     */
    public function getConsumerTag()
    {
        return $this->options['consumer_tag'];
    }

    /**
     * @param $noAck
     */
    public function setNoAck($noAck)
    {
        $this->options['no_ack'] = $noAck;
    }

    /**
     * @return bool
     */
    public function getNoAck()
    {
        return $this->options['no_ack'];
    }

    /**
     * @param $noLocal
     */
    public function setNoLocal($noLocal)
    {
        $this->options['no_local'] = $noLocal;
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
            $this->getDeclareExclusive(),
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
            $this->getNoLocal(),
            $this->getNoAck(),
            $this->getConsumeExclusive(),
            $this->getNoWait(),
            $this->getTicket(),
            $this->getArguments()
        ];
    }

    public function getExchangeDeclare()
    {
        return [
            $this->getExchange(),
            $this->getType(),
            $this->getPassive(),
            $this->getDurable(),
            $this->getAutoDelete(),
            $this->getInternal(),
            $this->getNoWait(),
            $this->getArguments(),
            $this->getTicket()
        ];
    }

    /**
     * @return string|mixed
     */
    public function getQueue()
    {
        return $this->getQueueName();
    }

    /**
     * @return bool
     */
    public function getExclusive()
    {
        return false;
    }
}