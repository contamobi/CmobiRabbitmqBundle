<?php

namespace Cmobi\RabbitmqBundle\Rpc\Response;

interface RpcResponseInterface
{
    /**
     * @return string|int
     */
    public function getId();

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