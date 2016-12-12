<?php

namespace Cmobi\RabbitmqBundle\Worker;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnectionInterface;
use Cmobi\RabbitmqBundle\Connection\Exception\InvalidAMQPChannelException;
use Cmobi\RabbitmqBundle\Queue\Queue;
use Cmobi\RabbitmqBundle\Queue\QueueBuilderInterface;
use Cmobi\RabbitmqBundle\Queue\QueueServiceInterface;
use Psr\Log\LoggerInterface;

class WorkerBuilder implements QueueBuilderInterface
{
    private $connection;
    private $channel;
    private $logger;
    private $parameters;

    public function __construct(CmobiAMQPConnectionInterface $connection, LoggerInterface $logger, array $parameters)
    {
        $this->connection = $connection;
        $this->logger = $logger;
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
        $rpcQueueBag = new WorkerQueueBag($queueName, $qos);

        $queue = new Queue($this->getChannel(), $rpcQueueBag, $this->logger);
        $queueCallback = new WorkerQueueCallback($queueService);
        $queue->setCallback($queueCallback);

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