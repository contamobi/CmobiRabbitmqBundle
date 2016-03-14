<?php

namespace Cmobi\RabbitmqBundle\Rpc\Request;

interface RpcRequestInterface
{
    /**
     * fetch $key from $attributes
     *
     * @param $key
     * @return mixed
     */
    public function get($key);

    /**
     * @return array
     */
    public function toArray();

    /**
     * @param array $request
     * @return RpcRequestInterface
     */
    public function fromArray(array $request);
}