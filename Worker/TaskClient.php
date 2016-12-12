<?php

namespace Cmobi\RabbitmqBundle\Worker;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnectionInterface;
use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Queue\CmobiAMQPMessage;
use Cmobi\RabbitmqBundle\Queue\QueueProducerInterface;

class TaskClient implements QueueProducerInterface
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
     * @return void
     */
    public function publish($data, $expire = self::DEFAULT_TTL, $priority = self::PRIORITY_LOW)
    {
        $this->refreshChannel();
        $queueBag = new WorkerQueueBag(
            sprintf('callback_to_%s_from_%s_%s', $this->getQueueName(), $this->getFromName(), microtime())
        );
        $this->getChannel()->queueDeclare($queueBag->getQueueDeclare());
        $msg = new CmobiAMQPMessage(
            (string)$data,
            [
                'delivery_mode' => 2, // make message persistent
                'priority' => $priority
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

        if (! $connection->isConnected()) {
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
}