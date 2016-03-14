<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\ConnectionManagerInterface;
use Cmobi\RabbitmqBundle\Rpc\Exception\JsonRpcInvalidRequestException;
use Cmobi\RabbitmqBundle\Rpc\Request\JsonRpcRequest;
use Cmobi\RabbitmqBundle\Rpc\Response\JsonRpcResponse;
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

    public function __construct($queueName, ConnectionManagerInterface $manager, JsonRpcRequest $request = null)
    {
        $this->body = $request;
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

    public function refreshChannel()
    {
        $connection = $this->getConnection();
        $this->channel = $connection->channel();
    }

    /**
     * @return null
     * @throws JsonRpcInvalidRequestException
     */
    public function call()
    {
        if (!$this->body instanceof JsonRpcRequest) {
            throw new JsonRpcInvalidRequestException();
        }

        list($callbackQueue, ,) = $this->getChannel()->queue_declare(
            '', false, false, false, true
        );
        $this->callbackQueue = $callbackQueue;
        $this->getChannel()->basic_consume(
            $this->callbackQueue, '', false, false, false, false,
            [$this, 'onResponse']
        );
        $this->response = null;
        $this->correlationId = $this->body->getId();

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
        $this->getChannel()->close();
        $this->getConnection()->close();


        $response = new JsonRpcResponse();

        return $this->response;
    }

    /**
     * @param JsonRpcRequest $body
     */
    public function setMessage(JsonRpcRequest $body)
    {
        $this->body = (string)$body;
    }

    /**
     * @return JsonRpcRequest
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
     * @return AMQPStreamConnection
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