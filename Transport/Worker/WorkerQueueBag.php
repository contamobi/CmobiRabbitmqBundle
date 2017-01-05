<?php

namespace Cmobi\RabbitmqBundle\Transport\Worker;

use Cmobi\RabbitmqBundle\Queue\QueueBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkerQueueBag implements QueueBagInterface
{
    private $resolver;
    private $options;

    public function __construct(
        $queueName,
        $basicQos = 1,
        array $arguments = null
    ) {
        $this->resolver = new OptionsResolver();
        $this->resolver->setDefaults([
            'queue_name' => $queueName,
            'basic_qos' => $basicQos,
            'arguments' => $arguments
        ]);
        $this->options = $this->resolver->resolve([]);
    }

    /**
     * @param $queue
     */
    public function setQueue($queue)
    {
        $this->options['queue_name'] = $queue;
    }

    /**
     * @return string|mixed
     */
    public function getQueue()
    {
        return $this->options['queue_name'];
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
     * @return bool
     */
    public function getPassive()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function getDurable()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function getExclusive()
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
     * @return bool
     */
    public function getNoWait()
    {
        return false;
    }

    /**
     * @return null
     */
    public function getTicket()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getConsumerTag()
    {
        return '';
    }

    /**
     * @return bool
     */
    public function getNoAck()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function getNoLocal()
    {
        return false;
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

    /**
     * @param array $options
     * @return QueueBagInterface
     */
    public function registerOptions(array $options)
    {
        $this->options = $this->resolver->resolve($options);

        return $this;
    }
}
