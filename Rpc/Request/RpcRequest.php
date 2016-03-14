<?php

namespace Cmobi\RabbitmqBundle\Rpc\Request;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;


class RpcRequest implements RpcRequestInterface
{
    const VERSION = '2.0';

    public $id;
    public $method;
    public $attributes;

    public function __construct($id = null, $method = null, array $attributes = [])
    {
        $this->id = $id;
        $this->method = $method;
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

    public function export()
    {
        $rpc = [
            'id' => $this->id,
            'jsonrpc' => self::VERSION,
            'method' => $this->method,
            'params' => $this->attributes->all()
        ];

        return $rpc;
    }
}