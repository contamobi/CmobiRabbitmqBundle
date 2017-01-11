<?php

namespace Cmobi\RabbitmqBundle\Transport\Rpc;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Queue\CmobiAMQPMessage;
use Cmobi\RabbitmqBundle\Queue\QueueProducerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class RpcClient implements QueueProducerInterface
{
    private $connectionManager;
    private $channel;
    private $fromName;
    private $queueName;
    private $response;
    private $correlationId;
    private $callbackQueue;

    public function __construct($queueName, ConnectionManager $manager, $fromName = '')
    {
        $this->queueName = $queueName;
        $this->fromName = $fromName;
        $this->connectionManager = $manager;
    }

    /**
     * @param AMQPMessage $rep
     */
    public function onResponse(AMQPMessage $rep)
    {
        if ($rep->get('correlation_id') === $this->correlationId) {
            $this->response = $rep->getBody();
        }
    }

    /**
     * @return \PhpAmqpLib\Channel\AMQPChannel
     *
     * @throws \Cmobi\RabbitmqBundle\Connection\Exception\NotFoundAMQPConnectionFactoryException
     */
    public function refreshChannel()
    {
        $connection = $this->connectionManager->getConnection();

        if (!$connection->isConnected()) {
            $connection->reconnect();
        }
        $this->channel = $connection->channel();

        return $this->channel;
    }

    /**
     * @param $data
     * @param int $expire
     * @param int $priority
     */
    public function publish($data, $expire = self::DEFAULT_TTL, $priority = self::PRIORITY_LOW)
    {
        $this->refreshChannel();
        $this->correlationId = $this->generateCorrelationId();
        $queueBag = new RpcQueueBag(
            sprintf('callback_to_%s_from_%s_%s', $this->getQueueName(), $this->getFromName(), microtime())
        );
        $queueBag->setArguments([
            'x-expires' => ['I', $expire],
        ]);
        list($callbackQueue) = $this->getChannel()->queueDeclare($queueBag->getQueueDeclare());
        $this->callbackQueue = $callbackQueue;
        $consumeQueueBag = new RpcQueueBag($callbackQueue);

        $this->getChannel()->basicConsume(
            $consumeQueueBag->getQueueConsume(),
            [$this, 'onResponse']
        );
        $msg = new CmobiAMQPMessage(
            (string) $data,
            [
                'correlation_id' => $this->correlationId,
                'reply_to' => $this->callbackQueue,
                'priority' => $priority,
            ]
        );
        $this->getChannel()->basic_publish($msg, '', $this->getQueueName());

        while (!$this->response) {
            $this->getChannel()->wait(null, 0, ($expire / 1000));
        }
        $this->getChannel()->close();
        $this->connectionManager->getConnection()->close();
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
     * @todo unecessary method set, its only exists to run tests whitout stay jailed in infinite while waiting response.
     *
     * @param $content
     */
    public function setResponse($content)
    {
        $this->response = $content;
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /** @return string */
    public function generateCorrelationId()
    {
        return uniqid($this->getQueueName()).microtime();
    }

    /**
     * @return string
     */
    public function getCurrentCorrelationId()
    {
        return $this->correlationId;
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