<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\Rpc\Response\RpcResponseCollectionInterface;
use PhpAmqpLib\Message\AMQPMessage;

interface RpcServiceInterface
{
    /**
     * @return \Closure
     */
    public function createCallback();

    /**
     * @param RpcResponseCollectionInterface $responses
     * @param AMQPMessage $requestMessage
     * @return AMQPMessage
     */
    public function buildResponseMessage(RpcResponseCollectionInterface $responses, AMQPMessage $requestMessage);

    /**
     * @return string
     */
    public function getQueueName();

    /**
     * @return array
     */
    public function getQueueOptions();
}