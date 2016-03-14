<?php

namespace Cmobi\RabbitmqBundle\Rpc\Request;

use Cmobi\RabbitmqBundle\Rpc\Exception\JsonRpcInvalidParamsException;
use Cmobi\RabbitmqBundle\Rpc\Exception\JsonRpcUnsupportedProtocolException;

class RpcRequestFactory
{
    private $requiredFields = ['id', 'jsonrpc', 'method'];

    public function factory(array $requestMessage)
    {
        $this->validateRequiredFields($requestMessage);

        $request = new RpcRequest($requestMessage['params']);
        $request->setId($requestMessage['id']);
        $request->setMethod($requestMessage['method']);

        return $request;
    }

    public function validateRequiredFields(array $parameters)
    {
        $keys = array_intersect_key($parameters, array_flip($this->requiredFields));

        if (count($this->requiredFields) !=  count($keys)) {
            throw new JsonRpcInvalidParamsException();
        }

        if ($parameters['jsonrpc'] !== RpcRequest::VERSION) {
            throw new JsonRpcUnsupportedProtocolException();
        }
    }
}