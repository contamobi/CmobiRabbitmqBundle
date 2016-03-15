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

            if (!$request->attributes->has('error')) {

                $controller = $this->getResolver()->getController($request);
                $arguments = $this->getResolver()->getArguments($request, $controller);
                $response = call_user_func_array($controller, $arguments);

                if (!is_array($response)) {
                    $previous = new InvalidBodyAMQPMessageException('Invalid Body: Content should be array.');
                    $exception = new RpcInternalErrorException($previous);
                    $error = new RpcResponse($request->id, [], $exception);
                    $responses->add($error);
                } else {
                    $rpcResponse = new RpcResponse($request->id, $response);
                    $responses->add($rpcResponse);
                }
            } else {
                $error = $request->attributes->get('error');
                $request->attributes->remove('error');
                $rpcResponse = new RpcResponse($request->id, [], $error);
                $responses->add($rpcResponse);
            }
        }

        return $responses;
    }

    /**
     * @return RpcControllerResolver
     */
    public function getResolver()
    {
        if (is_null($this->resolver->getContainer())) {
            $this->resolver->setContainer($this->container);
        }

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