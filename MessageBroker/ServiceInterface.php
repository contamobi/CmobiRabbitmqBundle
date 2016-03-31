<?php

namespace Cmobi\RabbitmqBundle\MessageBroker;

use PhpAmqpLib\Message\AMQPMessage;

interface ServiceInterface
{
    /**
     * @return \Closure
     */
    public function createCallback();

    public function publish(AMQPMessage $message, $content);

    /**
     * @return string
     */
    public function getQueueName();

    /**
     * @return array
     */
    public function getQueueOptions();
}