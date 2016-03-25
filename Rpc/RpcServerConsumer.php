<?php

namespace Cmobi\RabbitmqBundle\Rpc;

/**
 * Cosumer to call another rpc server given current server
 *
 * Class RpcServerConsumer
 * @package Cmobi\RabbitmqBundle\Rpc
 */
class RpcServerConsumer extends RpcClient
{
    public function call($expire = RpcClient::DEFAULT_TTL)
    {
        $this->refreshChannel();
        return parent::call($expire);
    }
}