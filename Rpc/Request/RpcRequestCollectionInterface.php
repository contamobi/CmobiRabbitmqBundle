<?php

namespace Cmobi\RabbitmqBundle\Rpc\Request;

interface RpcRequestCollectionInterface
{
    const PRIORITY_MAX = 100;
    const PRIORITY_MIDDLE = 50;
    const PRIORITY_LOW = 0;

    public function add(RpcRequestInterface $request);

    /**
     * Convert priority to octal
     *
     * @return int
     */
    public function getPriority();

    /**
     * Erase rpc requests
     */
    public function clear();
}