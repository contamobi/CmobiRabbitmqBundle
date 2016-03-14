<?php

namespace Cmobi\RabbitmqBundle\Rpc\Response;

use Cmobi\RabbitmqBundle\Rpc\Exception\RpcGenericErrorException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class RpcResponse implements RpcResponseInterface
{
    const VERSION = '2.0';

    public $id;
    public $method;
    public $attributes;
    public $error;

    public function __construct($id = null, $method = null, array $attributes = [], RpcGenericErrorException $error = null)
    {
        $this->id = $id;
        $this->method = $method;
        $this->error = $error;
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
     * @return null|array
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param RpcGenericErrorException $error
     */
    public function setError(RpcGenericErrorException $error)
    {
        $this->error = $error;
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
            'result' => $this->attributes->all()
        ];

        if ($this->error instanceof RpcGenericErrorException) {
            $rpc['error'] = $this->error;
            unset($rpc['result']);
        }

        return $rpc;
    }
}