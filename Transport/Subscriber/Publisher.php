<?php

namespace Cmobi\RabbitmqBundle\Transport\Subscriber;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnectionInterface;
use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Queue\CmobiAMQPMessage;
use Cmobi\RabbitmqBundle\Queue\QueueProducerInterface;

class Publisher implements QueueProducerInterface
{
    private $connectionManager;
    private $channel;
    private $fromName;
    private $queueName;
    private $exchange;
    private $exchangeType;

    public function __construct(
        $exchange,
        $exchangeType = ExchangeType::FANOUT,
        ConnectionManager $manager,
        $fromName,
        $queueName = ''
    ) {
        $this->exchange = $exchange;
        $this->exchangeType = $exchangeType;
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
        $queueBag = new SubscriberQueueBag($this->getExchange(), $this->getExchangeType(), $this->getQueueName());
        $this->getChannel()->exchangeDeclare($queueBag->getExchangeDeclare());
        $msg = new CmobiAMQPMessage((string) $data);
        $this->getChannel()->basic_publish($msg, $queueBag->getExchange());

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
     * @return CmobiAMQPChannel
     */
    public function getChannel()
    {
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
     * Return caller name.
     *
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
        return $this->exchange;
    }

    /**
     * @return string
     */
    public function getExchangeType()
    {
        return $this->exchangeType;
    }
}
