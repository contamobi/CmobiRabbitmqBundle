<?php

namespace Cmobi\RabbitmqBundle\Queue;

interface QueueBagInterface
{
    /**
     * @return string|mixed
     */
    public function getQueue();

    /**
     * @return int
     */
    public function getBasicQos();

    /**
     * @return bool
     */
    public function getPassive();

    /**
     * @return bool
     */
    public function getDurable();

    /**
     * @return bool
     */
    public function getExclusive();

    /**
     * @return bool
     */
    public function getAutoDelete();

    /**
     * @return bool
     */
    public function getNoWait();

    /**
     * @return string
     */
    public function getArguments();

    /**
     * @return string
     */
    public function getTicket();

    /**
     * @return string
     */
    public function getConsumerTag();

    /**
     * @return string
     */
    public function getExchange();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return bool
     */
    public function getNoAck();

    /**
     * @return bool
     */
    public function getNoLocal();

    /**
     * @return array
     */
    public function getQueueDeclare();

    /**
     * @return array
     */
    public function getQueueConsume();

    /**
     * @return array|false
     */
    public function getExchangeDeclare();

    /**
     * @param array $options
     * @return QueueBagInterface
     */
    public function registerOptions(array $options);
}
