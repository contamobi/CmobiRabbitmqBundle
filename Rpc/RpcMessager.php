<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\Routing\MethodRouter;
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
    private $router;
    private $requestCollection;
    private $responseCollection;
    private $requestFactory;

    public function __construct(
        MethodRouter $router,
        RpcRequestCollectionInterface $requests,
        RpcResponseCollectionInterface $responses,
        JsonRpcRequestFactory $factory
    )
    {
        $this->router = $router;
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

    public function addRequest(RpcRequestInterface $request)
    {
        $this->requestCollection->add($request);
    }

    public function addResponse(RpcResponseInterface $response)
    {
        $this->responseCollection->add($response);
    }

    public function addRequestCollection(RpcRequestCollectionInterface $collection)
    {
        $this->requestCollection = $collection;
    }

    public function addResponseCollection(RpcResponseCollectionInterface $collection)
    {
        $this->responseCollection = $collection;
    }

    private function buildRequest($request)
    {
        try {
            $request = $this->requestFactory->factory($request);
            $this->router->setContext($request);

            if (!$request->attributes->has('_controller')) {
                $parameters = $this->router->match($request->getMethod());
                $request->attributes->add($parameters);
            }
            $this->requestCollection->add($request);
        } catch (JsonRpcGenericErrorException $e) {
            $response = new JsonRpcResponse([], $e);
            $this->responseCollection->add($response);
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