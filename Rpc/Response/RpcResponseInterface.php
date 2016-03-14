<?php

namespace Cmobi\RabbitmqBundle\Rpc\Response;

interface RpcResponseInterface
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
     * @param array $response
     * @return RpcResponseInterface
     */
    public function fromArray(array $response);
}