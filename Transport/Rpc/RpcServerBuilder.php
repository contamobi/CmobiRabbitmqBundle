<?php

namespace Cmobi\RabbitmqBundle\Transport\Rpc;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnectionInterface;
use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Connection\Exception\InvalidAMQPChannelException;
use Cmobi\RabbitmqBundle\Queue\Queue;
use Cmobi\RabbitmqBundle\Queue\QueueBuilderInterface;
use Cmobi\RabbitmqBundle\Queue\QueueServiceInterface;
use Psr\Log\LoggerInterface;

class RpcServerBuilder implements QueueBuilderInterface
{
    private $connectionManager;
    private $logger;
    private $parameters;

    public function __construct(ConnectionManager $connManager, LoggerInterface $logger, array $parameters)
    {
        $this->connectionManager = $connManager;
        $this->logger = $logger;
        $this->parameters = $parameters;
        $this->channel = null;
    }

    /**
     * @param $queueName
     * @param QueueServiceInterface $queueService
     *
     * @return Queue
     * @throws InvalidAMQPChannelException
     */
    public function buildQueue($queueName, QueueServiceInterface $queueService)
    {
        $qos = 1;

        if (array_key_exists('cmobi_rabbitmq.basic_qos', $this->parameters)) {
            $qos = $this->parameters['cmobi_rabbitmq.basic_qos'];
        }
        $rpcQueueBag = new RpcQueueBag($queueName, $qos);

        $queue = new Queue($this->getConnectionManager(), $rpcQueueBag, $this->logger);
        $queueCallback = new RpcQueueCallback($queueService);
        $queue->setCallback($queueCallback);

        return $queue;
    }

    /**
     * @return ConnectionManager
     */
    public function getConnectionManager()
    {
        return $this->connectionManager;
    }

    /**
     * @return string|false
     */
    public function getExchangeName()
    {
        return false;
    }

    /**
     * @return string|false
     */
    public function getExchangeType()
    {
        return false;
    }
}