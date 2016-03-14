<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\Routing\MethodRouter;
use Cmobi\RabbitmqBundle\Rpc\Exception\RpcGenericErrorException;
use Cmobi\RabbitmqBundle\Rpc\Exception\RpcInvalidRequestException;
use Cmobi\RabbitmqBundle\Rpc\Exception\RpcParserErrorException;
use Cmobi\RabbitmqBundle\Rpc\Request\RpcRequest;
use Cmobi\RabbitmqBundle\Rpc\Request\RpcRequestCollection;
use PhpAmqpLib\Message\AMQPMessage;

class RpcMessager
{
    private $router;

    public function __construct(MethodRouter $router)
    {
        $this->router = $router;
    }

    /**
     * @param AMQPMessage $message
     * @return RpcRequestCollection
     * @throws RpcParserErrorException
     */
    public function parseAMQPMessage(AMQPMessage $message)
    {
        $body = $message->body;
        $requestCollection = new RpcRequestCollection();

        try {
            /**
             * @var RpcRequestCollection $requests
             */
            $requests = json_decode($body, true);

            foreach ($requests as $requestArr) {

                if (!is_array($requestArr)) {
                    throw new RpcInvalidRequestException();
                }
                $request = new RpcRequest();
                $request->fromArray($requestArr);
                $this->buildRequest($request);
                $requestCollection->add($request);
            }
        } catch (\Exception $e) {
            throw new RpcParserErrorException();
        }

        return $requestCollection;
    }

    /**
     * @param $request
     * @return RpcRequest
     */
    private function buildRequest($request)
    {
        try {
            $this->router->setContext($request);

            if (!$request->attributes->has('_controller')) {
                $parameters = $this->router->match($request->getMethod());
                $request->attributes->add($parameters);
            }
        } catch (RpcGenericErrorException $e) {
            $request->attributes->add(['error' => $e]);
        }

        return $request;
    }
}