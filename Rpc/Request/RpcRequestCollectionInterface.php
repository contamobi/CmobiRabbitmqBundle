<?php

namespace Cmobi\RabbitmqBundle\Rpc\Request;

interface RpcRequestCollectionInterface
{
    public function add($id, RpcRequestInterface $request);

    public function __toString();
}