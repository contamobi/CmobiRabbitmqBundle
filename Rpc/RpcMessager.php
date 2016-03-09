<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\Rpc\Exception\JsonRpcGenericErrorException;
use Cmobi\RabbitmqBundle\Rpc\Exception\JsonRpcParserErrorException;
use Cmobi\RabbitmqBundle\Rpc\Request\JsonRpcRequestFactory;
use Cmobi\RabbitmqBundle\Rpc\Request\RpcRequestCollectionInterface;
use Cmobi\RabbitmqBundle\Rpc\Request\RpcRequestInterface;
use Cmobi\RabbitmqBundle\Rpc\Response\JsonRpcResponse;
use Cmobi\RabbitmqBundle\Rpc\Response\RpcResponseCollectionInterface;
use Cmobi\RabbitmqBundle\Rpc\Response\RpcResponseInterface;
use PhpAmqpLib\Message\AMQPMessage;

class RpcMessager
{
    private $requestCollection;
    private $responseCollection;
    private $requestFactory;

    public function __construct(RpcRequestCollectionInterface $requests, RpcResponseCollectionInterface $responses, JsonRpcRequestFactory $factory)
    {
        $this->requestCollection = $requests;
        $this->responseCollection = $responses;
        $this->requestFactory = $factory;
    }

    public function parseAMQPMessage(AMQPMessage $message)
    {
        $body = $message->body;
        try {
            $requests = json_decode($body, true);
        } catch (\Exception $e) {
            throw new JsonRpcParserErrorException();
        }

        if (!isset($requests[0])) {
            $this->buildRequest($requests);
        } else {

            foreach ($requests as $request) {
                $this->buildRequest($request);
            }
        }
    }

    public function addRequest($id, RpcRequestInterface $request)
    {
        $this->requestCollection->add($id, $request);
    }

    public function addResponse($id, RpcResponseInterface $response)
    {
        $this->responseCollection->add($id, $response);
    }

    private function buildRequest($request)
    {
        try {
            $request = $this->requestFactory->factory($request);
            $this->requestCollection->add(uniqid(), $request);
        } catch (JsonRpcGenericErrorException $e) {
            $response = new JsonRpcResponse([], $e);
            $id = uniqid();

            if (isset($requests['id'])) {
                $id = $requests['id'];
            }
            $this->responseCollection->add($id, $response);
        }
    }

    /**
     * @return RpcRequestCollectionInterface
     */
    public function getRequestCollection()
    {
        return $this->requestCollection;
    }

    /**
     * @return RpcResponseCollectionInterface
     */
    public function getResponseCollection()
    {
        return $this->responseCollection;
    }
}