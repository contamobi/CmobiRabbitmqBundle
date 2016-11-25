<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnectionInterface;
use Cmobi\RabbitmqBundle\Connection\Exception\InvalidAMQPChannelException;
use Cmobi\RabbitmqBundle\Queue\QueueBuilderInterface;
use CmobiRabbitmqBundle\Connection\CmobiAMQPChannel;

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

    public function buildQueue()
    {
        $qos = 1;

        if (array_key_exists('cmobi_rabbitmq.basic_qos', $this->parameters)) {
            $qos = $this->parameters['cmobi_rabbitmq.basic_qos'];
        }
        $this->getChannel()->basic_qos(null, $qos, null);
    }

    /**
     * @return CmobiAMQPConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }
}