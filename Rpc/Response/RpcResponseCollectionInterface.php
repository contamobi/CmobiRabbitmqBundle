<?php

namespace Cmobi\RabbitmqBundle\Rpc\Response;

interface RpcResponseCollectionInterface
{
    public function add(RpcResponseInterface $request);

    public function __toString();
}