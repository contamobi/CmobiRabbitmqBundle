<?php

namespace Cmobi\RabbitmqBundle\Rpc\Request;

interface RpcRequestCollectionInterface
{
    public function add(RpcRequestInterface $request);
}