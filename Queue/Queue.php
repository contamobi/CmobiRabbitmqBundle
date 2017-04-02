<?php

namespace Cmobi\RabbitmqBundle\Queue;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnection;
use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Connection\Exception\InvalidAMQPChannelException;
use Psr\Log\LoggerInterface;

class Queue implements QueueInterface
{
    private $connectionManager;
    private $connection;
    private $connectionName;
    private $channel;
    private $queueBag;
    private $logOutput;
    private $callback;

    public function __construct(
        ConnectionManager $connectionManager,
        QueueBagInterface $queueBag,
        $connectionName = 'default',
        QueueCallbackInterface $callback = null
    )
    {
        $this->connectionManager = $connectionManager;
        $this->connectionName = $connectionName;
        $this->connection = $this->getConnectionManager()->getConnection($connectionName);
        $this->queueBag = $queueBag;
        $this->logOutput = fopen('php://stdout', 'a+');
        $this->callback = $callback;
    }

    /**
     * @return CmobiAMQPChannel
     *
     * @throws InvalidAMQPChannelException
     */
    protected function getChannel()
    {
        if ($this->channel instanceof CmobiAMQPChannel) {
            return $this->channel;
        }
        $this->channel = $this->getConnection()->channel();

        if (!$this->channel instanceof CmobiAMQPChannel) {
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
            list($queueName) = $this->getChannel()->queueDeclare($queueBag->getQueueDeclare());
            $this->getChannel()->queue_bind($queueName, $queueBag->getExchange());
        } else {
            $this->getChannel()->queueDeclare($queueBag->getQueueDeclare());
        }
        $this->getChannel()->basicConsume($queueBag->getQueueConsume(), $this->getCallback()->toClosure());
    }

    /**
     * Declare and start queue in broker.
     */
    public function start()
    {
        $this->createQueue();

        while (count($this->getChannel()->callbacks)) {
            try {
                $this->getChannel()->wait();
            } catch (\Exception $e) {
                fwrite($this->logOutput, $e->getMessage());
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
    public function getQueuebag()
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
     * @return ConnectionManager
     */
    public function getConnectionManager()
    {
        return $this->connectionManager;
    }

    /**
     * @return CmobiAMQPConnection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Retry connect to message broker until it can.
     */
    /**
     * @param CmobiAMQPConnection|null $connection
     *
     * @return CmobiAMQPChannel
     */
    public function forceReconnect(CmobiAMQPConnection $connection = null)
    {
        do {
            try {
                $failed = false;
                fwrite($this->logOutput, 'start Queue::forceReconnect() - trying connect...' . PHP_EOL);
                $this->connection = $this->getConnectionManager()->getConnection($this->connectionName);
                $this->channel = $this->getConnection()->channel();
                $this->createQueue();
            } catch (\Exception $e) {
                $failed = true;
                sleep(3);
                fwrite($this->logOutput, 'failed Queue::forceReconnect() - ' . $e->getMessage() . PHP_EOL);
            }
        } while ($failed);
        fwrite($this->logOutput, 'Queue::forceReconnect() - connected!' . PHP_EOL);

        return $this->channel;
    }
}
