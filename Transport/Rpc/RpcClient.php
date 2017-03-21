<?php

namespace Cmobi\RabbitmqBundle\Transport\Rpc;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Queue\CmobiAMQPMessage;
use Cmobi\RabbitmqBundle\Queue\QueueProducerInterface;
use Cmobi\RabbitmqBundle\Transport\Exception\QueueNotFoundException;
use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;

class RpcClient implements QueueProducerInterface
{
    private $connectionManager;
    private $connection;
    private $channel;
    private $fromName;
    private $queueName;
    private $response;
    private $correlationId;
    private $callbackQueue;

    public function __construct($queueName, ConnectionManager $manager, $fromName)
    {
        $this->queueName = $queueName;
        $this->fromName = $fromName;
        $this->connectionManager = $manager;
        $this->connection = $this->connectionManager->getConnection();
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
        if (! $this->connection->isConnected()) {
            $this->connection->reconnect();
        }
        $this->channel = $this->connection->channel();

        return $this->channel;
    }

    /**
     * @param $data
     * @param int $expire
     * @param int $priority
     * @throws QueueNotFoundException
     * @throws \Cmobi\RabbitmqBundle\Connection\Exception\NotFoundAMQPConnectionFactoryException
     */
    public function publish($data, $expire = self::DEFAULT_TTL, $priority = self::PRIORITY_LOW)
    {
        $this->response = null;
        $this->refreshChannel();

        if (! $this->queueHasExists()) {
            throw new QueueNotFoundException("Queue $this->queueName not declared.");
        }
        $this->correlationId = $this->generateCorrelationId();
        $queueBag = new RpcQueueBag(
            sprintf(
                'callback_to_%s_from_%s_%s',
                $this->getQueueName(),
                $this->getFromName(),
                Uuid::uuid4()->toString()
                . microtime()
            )
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

        while (! $this->response) {
            $this->getChannel()->wait(null, 0, ($expire / 1000));
        }
        $this->getChannel()->close();
        $this->connectionManager->getConnection()->close();
    }

    /**
     * @return bool
     */
    public function queueHasExists()
    {
        try {
            $this->getChannel()->queue_declare($this->queueName, true);
        } catch (\Exception $e) {
            return false;
        }

        return true;
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
        return uniqid($this->getQueueName()) . Uuid::uuid4()->toString() . microtime();
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
