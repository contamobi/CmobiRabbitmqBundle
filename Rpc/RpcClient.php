<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\ConnectionManagerInterface;
use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

abstract class RpcClient
{
    private $queue;
    private $connection;
    private $body;
    private $channel;
    private $callbackQueue;
    private $response;
    private $correlationId;

    public function __construct($queueName, ConnectionManagerInterface $manager)
    {
        $this->body = '';
        $this->queue = $queueName;
        $this->connection = $manager->getConnection();
        $this->channel = $this->connection->channel();
    }

    /**
     * @param AMQPMessage $rep
     */
    public function onResponse(AMQPMessage $rep)
    {
        if($rep->get('correlation_id') == $this->correlationId) {
            $this->response = $rep->body;
        }
    }

    /**
     * @return null|string
     */
    public function call()
    {
        list($callbackQueue, ,) = $this->getChannel()->queue_declare(
            '', false, false, true, false
        );
        $this->callbackQueue = $callbackQueue;
        $this->getChannel()->basic_consume(
            $this->callbackQueue, '', false, false, false, false,
            [$this, 'onResponse']
        );
        $this->response = null;
        $this->correlationId = uniqid();

        $msg = new AMQPMessage(
            $this->getMessage(),
            [
                'correlation_id' => $this->correlationId,
                'reply_to' => $this->callbackQueue
            ]
        );
        $this->getChannel()->basic_publish($msg, '', $this->getQueueName());

        while(!$this->response) {
            $this->getChannel()->wait();
        }
        return $this->response;
    }

    /**
     * @param string $body
     */
    public function declareMessage($body)
    {
        $this->body = (string)$body;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->body;
    }

    /**
     * @param AbstractChannel $channel
     */
    public function setChannel(AbstractChannel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return \PhpAmqpLib\Channel\AMQPChannel
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
        return $this->queue;
    }

    /**
     * @return ConnectionManagerInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param \PhpAmqpLib\Connection\AMQPStreamConnection $connection
     */
    public function setConnection(AMQPStreamConnection $connection)
    {
        $this->connection = $connection;
    }
}