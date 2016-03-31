<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\MessageBroker\ServiceInterface;
use Cmobi\RabbitmqBundle\Rpc\Response\RpcResponseCollectionInterface;
use PhpAmqpLib\Message\AMQPMessage;

interface RpcServiceInterface extends ServiceInterface
{
    /**
     * @param RpcResponseCollectionInterface $responses
     * @param AMQPMessage $requestMessage
     * @return AMQPMessage
     */
    public function buildResponseMessage(RpcResponseCollectionInterface $responses, AMQPMessage $requestMessage);
}