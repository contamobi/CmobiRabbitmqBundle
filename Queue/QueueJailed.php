<?php

namespace Cmobi\RabbitmqBundle\Queue;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnection;
use Cmobi\RabbitmqBundle\Connection\ConnectionFactory;
use Cmobi\RabbitmqBundle\Connection\Exception\InvalidAMQPChannelException;
use Psr\Log\LoggerInterface;

class QueueJailed extends \Threaded implements QueueInterface
{
    private $connectionFactory;
    private $connection;
    private $channel;
    private $callback;
    private $queueBag;
    private $logger;

    public function __construct(ConnectionFactory $connFactory, QueueBagInterface $queueBag, LoggerInterface $logger)
    {
        $this->connectionFactory = $connFactory;
        $this->queueBag = $queueBag;
        $this->logger = $logger;
    }

    /**
     * @return CmobiAMQPChannel
     * @throws InvalidAMQPChannelException
     */
    protected function getChannel()
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

    protected function createQueue()
    {
        $queueBag = $this->getQueuebag();

        $this->getChannel()->basic_qos(null, $queueBag->getBasicQos(), null);

        if ($queueBag->getExchangeDeclare()) {
            $this->getChannel()->exchangeDeclare($queueBag->getExchangeDeclare());
            list ($queueName, , ) = $this->getChannel()->queueDeclare($queueBag->getQueueDeclare());
            $this->getChannel()->queue_bind($queueName, $queueBag->getExchange());
        } else {
            $this->getChannel()->queueDeclare($queueBag->getQueueDeclare());
        }
        $this->getChannel()->basicConsume($queueBag->getQueueConsume(), $this->getCallback()->toClosure());
    }

    /**
     * Declare and start queue in broker
     */
    public function start()
    {
        $this->run();
    }

    public function run()
    {
        $this->createQueue();

        while(count($this->getChannel()->callbacks)) {
            try {
                $this->getChannel()->wait();
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                $this->forceReconnect();

                continue;
            }
        }
        $connection = $this->getChannel()->getConnection();
        $this->getChannel()->close();
        $connection->close();
    }

    /**
     * @return QueueBagInterface
     */
    public function getQueueBag()
    {
        return $this->queueBag;
    }

    /**
     * @param QueueCallbackInterface $callback
     */
    public function setCallback(QueueCallbackInterface $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return QueueCallbackInterface
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return CmobiAMQPConnection
     */
    public function getConnection()
    {
        if (! $this->connection) {
            $this->connection = $this->connectionFactory->createConnection();
        }
        return $this->connection;
    }

    /**
     * Retry connect to message broker until it can.
     */
    public function forceReconnect()
    {
        do {
            try {
                $failed = false;
                $this->logger->warning('forceReconnect() - trying connect...');
                $this->connection = $this->connectionFactory->createConnection();
                $this->channel = $this->getConnection()->channel();
                $this->createQueue();
            } catch (\Exception $e) {
                $failed = true;
                sleep(3);
                $this->logger->error('forceReconnect() - ' . $e->getMessage());
            }
        } while ($failed);
        $this->logger->warning('forceReconnect() - connected!');

        return $this->channel;
    }
}