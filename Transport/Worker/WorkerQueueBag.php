<?php

namespace Cmobi\RabbitmqBundle\Transport\Worker;

use Cmobi\RabbitmqBundle\Queue\QueueBagInterface;

class WorkerQueueBag implements QueueBagInterface
{
    private $options;

    public function __construct(
        $queue,
        $basicQos = 1,
        $passive = false,
        $durable = true,
        $exclusive = false,
        $autoDelete = false,
        $noWait = false,
        array $arguments = null,
        $ticket = null,
        $consumerTag = '',
        $noAck = false,
        $noLocal = false
    ) {
        $this->options = [
            'queue' => $queue,
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
            'no_local' => $noLocal,
        ];
    }

    /**
     * @param $queue
     */
    public function setQueue($queue)
    {
        $this->options['queue'] = $queue;
    }

    /**
     * @return string|mixed
     */
    public function getQueue()
    {
        return $this->options['queue'];
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
    public function setExclusive($exclusive)
    {
        $this->options['exclusive'] = $exclusive;
    }

    /**
     * @return bool
     */
    public function getExclusive()
    {
        return $this->options['exclusive'];
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
            $this->getQueue(),
            $this->getPassive(),
            $this->getDurable(),
            $this->getExclusive(),
            $this->getAutoDelete(),
            $this->getNoWait(),
            $this->getArguments(),
            $this->getTicket(),
        ];
    }

    /**
     * @return array
     */
    public function getQueueConsume()
    {
        return [
            $this->getQueue(),
            $this->getConsumerTag(),
            $this->getNoLocal(),
            $this->getNoAck(),
            $this->getExclusive(),
            $this->getNoWait(),
            $this->getTicket(),
            $this->getArguments(),
        ];
    }

    /**
     * @return array|false
     */
    public function getExchangeDeclare()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getExchange()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return false;
    }
}
