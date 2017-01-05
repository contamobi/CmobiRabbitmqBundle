<?php

namespace Cmobi\RabbitmqBundle\Transport\PubSub;

use Cmobi\RabbitmqBundle\Queue\QueueBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriberQueueBag implements QueueBagInterface
{
    private $resolver;
    private $options;

    public function __construct(
        $exchangeName,
        $type = ExchangeType::FANOUT,
        $queueName = null,
        $basicQos = 1,
        array $arguments = null
    ) {
        $this->resolver = new OptionsResolver();
        $this->resolver->setDefaults([
            'exchange' => $exchangeName,
            'type' => $type,
            'queue_name' => $queueName,
            'basicQos' => $basicQos,
            'arguments' => $arguments
        ]);
        $this->options = $this->resolver->resolve([]);
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
    public function setQueue($queueName)
    {
        $this->options['queue_name'] = $queueName;
    }


    /**
     * @return string|mixed
     */
    public function getQueue()
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
     * @return bool
     */
    public function getPassive()
    {
        return false;
    }

    /**
     * @param $internal
     */
    public function setInternal($internal)
    {
        $this->options['internal'] = $internal;
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
     * @return bool
     */
    public function getInternal()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function getDurable()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function getAutoDelete()
    {
        return false;
    }


    /**
     * @return string
     */
    public function getTicket()
    {
        return null;
    }

    /**
     * @return bool
     */
    public function getNoAck()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function getNoLocal()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function getNoWait()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function getExclusive()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getConsumerTag()
    {
        return '';
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
            true,
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
            false,
            $this->getNoWait(),
            $this->getTicket(),
            $this->getArguments(),
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
            $this->getTicket(),
        ];
    }

    /**
     * @param array $options
     * @return QueueBagInterface
     */
    public function registerOptions(array $options)
    {
        $this->options = $this->resolver->resolve($options);
    }
}
