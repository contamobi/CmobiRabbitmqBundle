<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnectionInterface;
use Cmobi\RabbitmqBundle\Connection\Exception\InvalidAMQPChannelException;
use Cmobi\RabbitmqBundle\Queue\Queue;
use Cmobi\RabbitmqBundle\Queue\QueueBuilderInterface;

class RpcServerBuilder implements QueueBuilderInterface
{
    private $connection;
    private $channel;
    private $parameters;

    public function __construct(CmobiAMQPConnectionInterface $connection, array $parameters)
    {
        $this->connection = $connection;
        $this->parameters = $parameters;
        $this->channel = null;
    }

    /**
     * @return CmobiAMQPChannel
     * @throws InvalidAMQPChannelException
     */
    public function getChannel()
    {
        if ($this->channel instanceof CmobiAMQPChannel) {
            return $this->channel;
        }
        $this->channel = $this->getConnection()->channel();

        if (! $this->channel instanceof CmobiAMQPChannel) {
            throw new InvalidAMQPChannelException('Failed get AMQPChannel');
        }

        return $this->channel;
    }

    /**
     * @param $queueName
     * @return Queue
     * @throws InvalidAMQPChannelException
     */
    public function buildQueue($queueName)
    {
        $qos = 1;

        if (array_key_exists('cmobi_rabbitmq.basic_qos', $this->parameters)) {
            $qos = $this->parameters['cmobi_rabbitmq.basic_qos'];
        }
        $rpcQueueBag = new RpcQueueBag($queueName, $qos);

        $queue = new Queue($this->getChannel(), $rpcQueueBag);

        return $queue;
    }

    /**
     * @return CmobiAMQPConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }
}