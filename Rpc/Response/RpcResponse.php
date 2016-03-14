<?php

namespace Cmobi\RabbitmqBundle\Rpc\Response;

use Cmobi\RabbitmqBundle\Rpc\Exception\RpcGenericErrorException;
use Cmobi\RabbitmqBundle\Rpc\Exception\RpcInvalidResponseException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class RpcResponse implements RpcResponseInterface
{
    const VERSION = '2.0';

    public $id;
    public $attributes;
    public $error;

    public function __construct($id = null, array $attributes = [], RpcGenericErrorException $error = null)
    {
        $this->id = $id;
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

    /**
     * @return array
     */
    public function toArray()
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

    /**
     * @param array $response
     * @return $this
     * @throws RpcInvalidResponseException
     */
    public function fromArray(array $response)
    {
        $this->validate($response);
        $this->id = $response['id'];

        if (isset($response['error'])) {
            $this->error = $response['error'];
        }

        if (isset($response['result'])) {

            $result = $response['result'];

            if (!is_array($result)) {
                $result = [$result];
            }
            $this->attributes = new ParameterBag($result);
        }

        return $this;
    }

    /**
     * @param array $response
     * @throws RpcInvalidResponseException
     */
    public function validate(array $response)
    {
        if (
            !array_key_exists(['id', 'jsonrpc'], $response)
            || (!isset($response['result'])
                && !isset($response['error']))
        ) {
            throw new RpcInvalidResponseException();
        }
    }
}