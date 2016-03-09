<?php

namespace Cmobi\RabbitmqBundle\Rpc\Request;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class JsonRpcRequest implements RpcRequestInterface
{
    const VERSION = '2.0';

    public $id;
    public $method;
    public $attributes;

    public function __construct(array $attributes = [])
    {
        $this->attributes = new ParameterBag($attributes);
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->attributes->get($key);
    }

    public function __toString()
    {
        $jsonRpc = [
            'id' => $this->id,
            'jsonrpc' => self::VERSION,
            'method' => $this->method,
            'params' => $this->attributes->all()
        ];

        return json_encode($jsonRpc);
    }
}