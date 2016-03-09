<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\Rpc\Controller\RpcControllerResolver;
use Cmobi\RabbitmqBundle\Rpc\Exception\InvalidBodyAMQPMessageException;
use Cmobi\RabbitmqBundle\Rpc\Exception\JsonRpcInternalErrorException;
use Cmobi\RabbitmqBundle\Rpc\Request\RpcRequestCollectionInterface;
use Cmobi\RabbitmqBundle\Rpc\Response\JsonRpcResponse;
use Cmobi\RabbitmqBundle\Rpc\Response\RpcResponseCollectionInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Handler
{
    use ContainerAwareTrait;

    private $resolver;

    public function __construct()
    {
        $this->resolver = new RpcControllerResolver();
    }

    public function handle(RpcRequestCollectionInterface $requests, RpcResponseCollectionInterface $responses)
    {
        foreach ($requests as $request) {
            $controller = $this->getResolver()->getController($request);
            $arguments = $this->getResolver()->getArguments($request, $controller);
            $response = call_user_func_array($controller, $arguments);

            if (!is_string($response) || is_null($response)) {
                $previous = new InvalidBodyAMQPMessageException('Invalid Body: Content should be string and not null.');
                $exception = new JsonRpcInternalErrorException($previous);
                $error = new JsonRpcResponse([], $exception);
                $error->setId($request->id);
                $error->setMethod($request->method);
                $responses->add($request->id, $error);
            } else {
                $responses->add(null, $response);
            }
        }
        return $responses;
    }

    /**
     * @return RpcControllerResolver
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}