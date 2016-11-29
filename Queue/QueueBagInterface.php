<?php

namespace Cmobi\RabbitmqBundle\Queue;

interface QueueBagInterface
{
    /**
     * @return string
     */
    public function getQueueName();

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
}