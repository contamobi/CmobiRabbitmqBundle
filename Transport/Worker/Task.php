<?php

namespace Cmobi\RabbitmqBundle\Transport\Worker;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnectionInterface;
use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Queue\CmobiAMQPMessage;
use Cmobi\RabbitmqBundle\Queue\QueueProducerInterface;

class Task implements QueueProducerInterface
{
    private $connectionManager;
    private $channel;
    private $fromName;
    private $queueName;

    public function __construct($queueName, ConnectionManager $manager, $fromName = '')
    {
        $this->queueName = $queueName;
        $this->fromName = $fromName;
        $this->connectionManager = $manager;
    }

    /**
     * @param $data
     * @param $expire
     * @param $priority
     */
    public function publish($data, $expire = self::DEFAULT_TTL, $priority = self::PRIORITY_LOW)
    {
        $this->refreshChannel();
        $queueBag = new WorkerQueueBag($this->getQueueName());
        $this->getChannel()->queueDeclare($queueBag->getQueueDeclare());
        $msg = new CmobiAMQPMessage(
            (string) $data,
            [
                'delivery_mode' => 2, // make message persistent
                'priority' => $priority,
            ]
        );
        $this->getChannel()->basic_publish($msg, '', $this->getQueueName());

        $this->getChannel()->close();
        $this->connectionManager->getConnection()->close();
    }

    /**
     * @return CmobiAMQPChannel
     */
    public function refreshChannel()
    {
        /** @var CmobiAMQPConnectionInterface $connection */
        $connection = $this->connectionManager->getConnection();

        if (!$connection->isConnected()) {
            $connection->reconnect();
        }
        $this->channel = $connection->channel();

        return $this->channel;
    }

    /**
     * @return string
     */
    public function getQueueName()
    {
        return $this->queueName;
    }

    /**
     * @return CmobiAMQPChannel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @return string
     */
    public function getExchange()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getExchangeType()
    {
        return false;
    }
}