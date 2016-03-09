<?php

namespace Cmobi\RabbitmqBundle\Rpc\Response;

use Cmobi\RabbitmqBundle\Rpc\Exception\JsonRpcGenericErrorException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class JsonRpcResponse implements RpcResponseInterface
{
    const VERSION = '2.0';

    public $id;
    public $method;
    public $attributes;
    public $error;

    public function __construct(array $attributes = [], JsonRpcGenericErrorException $error = null)
    {
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
     * @param JsonRpcGenericErrorException $error
     */
    public function setError(JsonRpcGenericErrorException $error)
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

    public function __toString()
    {
        $jsonRpc = [
            'id' => $this->id,
            'jsonrpc' => self::VERSION,
            'result' => $this->attributes->all()
        ];

        if ($this->error instanceof JsonRpcGenericErrorException) {
            $error = json_decode((string)$this->error, true);
            $jsonRpc['error'] = $error;
            unset($jsonRpc['result']);
        }

        return json_encode($jsonRpc);
    }
}