<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\ConnectionManagerInterface;
use Cmobi\RabbitmqBundle\Rpc\Exception\RpcInvalidRequestException;
use Cmobi\RabbitmqBundle\Rpc\Exception\RpcInvalidResponseException;
use Cmobi\RabbitmqBundle\Rpc\Request\RpcRequest;
use Cmobi\RabbitmqBundle\Rpc\Request\RpcRequestCollection;
use Cmobi\RabbitmqBundle\Rpc\Request\RpcRequestCollectionInterface;
use Cmobi\RabbitmqBundle\Rpc\Request\RpcRequestInterface;
use Cmobi\RabbitmqBundle\Rpc\Response\RpcResponse;
use Cmobi\RabbitmqBundle\Rpc\Response\RpcResponseCollection;
use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

abstract class RpcClient
{
    private $queue;
    private $connection;
    private $channel;
    private $callbackQueue;
    private $response;
    private $requestCollection;
    private $correlationId;

    public function __construct($queueName, ConnectionManagerInterface $manager)
    {
        $this->queue = $queueName;
        $this->connection = $manager->getConnection();
        $this->channel = $this->connection->channel();
        $this->requestCollection = new RpcRequestCollection();
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
     * @return RpcResponseCollection
     * @throws RpcInvalidRequestException
     * @throws RpcInvalidResponseException
     */
    public function call()
    {
        if (
            !$this->requestCollection instanceof RpcRequestCollection
            || $this->requestCollection->count() < 1
        ) {
            throw new RpcInvalidRequestException();
        }
        $requestId = uniqid($this->queue);
        $this->correlationId = $requestId;
        $requests = [];

        /**
         * @var RpcRequest $request
         */
        foreach ($this->requestCollection as $request) {

            if (is_null($request->id)) {
                $request->id = $requestId;
            }
            $requests[] = $request->toArray();
        }

        try {
            $body = json_encode($requests);
        } catch (\Exception $e) {
            throw new RpcInvalidRequestException($e);
        }
        /* Send to Message Broker */
        $this->handleRequest($body, $this->requestCollection->getPriority());
       $rpcResponse = $this->buildRpcResponseCollection();

        return $rpcResponse;
    }

    /**
     * @param RpcRequest $request
     */
    public function addRequest(RpcRequest $request)
    {
        $this->requestCollection->add($request);
    }

    /**
     * @param RpcRequest $request
     */
    public function removeRequest(RpcRequest $request)
    {
        $id = $this->requestCollection->getRequestIndex($request);

        if (!$id) {
            $this->requestCollection->remove($id);
        }
    }

    /**
     * @param RpcRequestCollection $requests
     */
    public function addRequestCollection(RpcRequestCollection $requests)
    {
        $this->requestCollection = $requests;
    }

    /**
     * @return RpcRequestCollection
     */
    public function getRequestCollection()
    {
        return $this->requestCollection;
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

    /**
     * @param $body
     * @param int $priority
     */
    private function handleRequest($body, $priority = RpcRequestCollectionInterface::PRIORITY_LOW)
    {
        list($callbackQueue, ,) = $this->getChannel()->queue_declare(
            '', false, false, false, true
        );
        $this->callbackQueue = $callbackQueue;
        $this->getChannel()->basic_consume(
            $this->callbackQueue, '', false, false, false, false,
            [$this, 'onResponse']
        );

        $msg = new AMQPMessage(
            (string)$body,
            [
                'correlation_id' => $this->correlationId,
                'reply_to' => $this->callbackQueue,
                'priority' => $priority
            ]
        );
        $this->getChannel()->basic_publish($msg, '', $this->getQueueName());

        while(!$this->response) {
            $this->getChannel()->wait();
        }
        $this->getChannel()->close();
        $this->getConnection()->close();
    }

    /**
     * @return RpcResponseCollection
     * @throws RpcInvalidResponseException
     */
    private function buildRpcResponseCollection()
    {
        $responses = [];
        try {
            $responses = json_decode($this->response, true);
        } catch (\Exception $e) {
            throw new RpcInvalidResponseException($e);
        }
        $rpcResponse = new RpcResponseCollection();

        foreach ($responses as $responseArr) {
            $response = new RpcResponse();
            $response->fromArray($responseArr);
            $rpcResponse->add($response);
        }

        return $rpcResponse;
    }
}