<?php

namespace Cmobi\RabbitmqBundle\Rpc\Response;

interface RpcResponseCollectionInterface
{
    public function add($id = null, RpcResponseInterface $request);

    public function __toString();
}