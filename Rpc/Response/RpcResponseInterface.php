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
}