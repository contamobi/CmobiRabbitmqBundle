<?php

namespace Cmobi\RabbitmqBundle\Transport\Worker;

use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Connection\Exception\InvalidAMQPChannelException;
use Cmobi\RabbitmqBundle\Queue\Queue;
use Cmobi\RabbitmqBundle\Queue\QueueBagInterface;
use Cmobi\RabbitmqBundle\Queue\QueueBuilderInterface;
use Cmobi\RabbitmqBundle\Queue\QueueServiceInterface;
use Psr\Log\LoggerInterface;

class WorkerBuilder implements QueueBuilderInterface
{
    private $queue;
    private $connectionManager;
    private $connectionName;
    private $logger;

    public function __construct(ConnectionManager $connManager, LoggerInterface $logger, $connectionName = 'default')
    {
        $this->connectionManager = $connManager;
        $this->connectionName = $connectionName;
        $this->logger = $logger;
        $this->channel = null;
    }

    /**
     * @param $queueName
     * @param QueueServiceInterface $queueService
     * @param QueueBagInterface $queueBag
     * @return Queue
     * @throws \Exception
     */
    public function buildQueue($queueName, QueueServiceInterface $queueService, QueueBagInterface $queueBag)
    {
        if (! $queueBag instanceof WorkerQueueBag) {
            throw new \Exception('Unsupported QueueBag');
        }
        $queue = new Queue($this->getConnectionManager(), $queueBag, $this->logger, $this->connectionName);
        $queueCallback = new WorkerQueueCallback($queueService);
        $queue->setCallback($queueCallback);
        $this->queue = $queue;

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
     * @return Queue
     */
    public function getQueue()
    {
        return $this->queue;
    }
}
