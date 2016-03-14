<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\Rpc\Controller\RpcControllerResolver;
use Cmobi\RabbitmqBundle\Rpc\Exception\InvalidBodyAMQPMessageException;
use Cmobi\RabbitmqBundle\Rpc\Exception\RpcInternalErrorException;
use Cmobi\RabbitmqBundle\Rpc\Request\RpcRequestCollectionInterface;
use Cmobi\RabbitmqBundle\Rpc\Response\RpcResponse;
use Cmobi\RabbitmqBundle\Rpc\Response\RpcResponseCollection;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Handler
{
    use ContainerAwareTrait;

    private $resolver;

    public function __construct()
    {
        $this->resolver = new RpcControllerResolver();
    }

    /**
     * @param RpcRequestCollectionInterface $requests
     * @return RpcResponseCollection
     */
    public function handle(RpcRequestCollectionInterface $requests)
    {
        $responses = new RpcResponseCollection();

        foreach ($requests as $request) {

            if (!$request->attributes->get('error')) {

                $controller = $this->getResolver()->getController($request);
                $arguments = $this->getResolver()->getArguments($request, $controller);
                $response = call_user_func_array($controller, $arguments);

                if (!is_array($response)) {
                    $previous = new InvalidBodyAMQPMessageException('Invalid Body: Content should be array.');
                    $exception = new RpcInternalErrorException($previous);
                    $error = new RpcResponse($request->id, $request->method, $request->attributes, $exception);
                    $responses->add($error);
                } else {
                    $response = new RpcResponse($response);
                    $response->setId($request->id);
                    $response->setMethod($request->method);
                    $responses->add($response);
                }
            } else {
                $error = $request->attributes->get('error');
                $request->attributes->remove('error');
                $response = new RpcResponse($request->id, $request->method, $request, $error);
                $responses->add($response);
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