<?php

namespace Cmobi\RabbitmqBundle\Rpc\Request;

use Cmobi\RabbitmqBundle\Rpc\Exception\RpcInvalidRequestException;
use Symfony\Component\HttpFoundation\ParameterBag;


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
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->attributes = new ParameterBag($params);
    }

    public function getParams()
    {
        return $this->attributes->all();
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

    /**
     * @param $key
     * @param $value
     */
    public function add($key, $value)
    {
        $this->attributes->add([$key => $value]);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $rpc = [
            'id' => $this->id,
            'jsonrpc' => self::VERSION,
            'method' => $this->method,
            'params' => $this->attributes->all()
        ];

        return $rpc;
    }

    /**
     * @param array $request
     * @return $this
     * @throws RpcInvalidRequestException
     */
    public function fromArray(array $request)
    {
        $this->validate($request);
        $this->id = $request['id'];
        $this->method = $request['method'];
        $this->attributes = new ParameterBag($request['params']);

        return $this;
    }

    public function validate(array $request)
    {
        if (
            (!isset($request['id']) || !isset($request['method']) || !isset($request['params']))
            || self::VERSION !== $request['jsonrpc']
            || !is_array($request['params'])
        ) {
            throw new RpcInvalidRequestException();
        }
    }
}