<?php

namespace Cmobi\RabbitmqBundle\Transport\PubSub;

use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Connection\Exception\InvalidAMQPChannelException;
use Cmobi\RabbitmqBundle\Queue\Queue;
use Cmobi\RabbitmqBundle\Queue\QueueBuilderInterface;
use Cmobi\RabbitmqBundle\Queue\QueueServiceInterface;
use Psr\Log\LoggerInterface;

class SubscriberBuilder implements QueueBuilderInterface
{
    private $exchangeName;
    private $exchangeType;
    private $connectionManager;
    private $logger;
    private $parameters;

    public function __construct(
        $exchangeName,
        $exchangeType = ExchangeType::FANOUT,
        ConnectionManager $connManager,
        LoggerInterface $logger,
        array $parameters
    )
    {
        $this->exchangeName = $exchangeName;
        $this->exchangeType = $exchangeType;
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
        $subQueueBag = new SubscriberQueueBag($this->getExchangeName(), $this->getExchangeType(), $queueName, $qos);

        $queue = new Queue($this->getConnectionManager(), $subQueueBag, $this->logger);
        $queueCallback = new SubscriberQueueCallback($queueService);
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
        return $this->exchangeName;
    }

    /**
     * @return string|false
     */
    public function getExchangeType()
    {
        return $this->exchangeType;
    }
}