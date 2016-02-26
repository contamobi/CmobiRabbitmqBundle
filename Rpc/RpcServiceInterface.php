<?php

namespace Cmobi\RabbitmqBundle\Rpc;

interface RpcServiceInterface
{
    /**
     * @return \Closure
     */
    public function createCallback();

    /**
     * @return string
     */
    public function getQueueName();

    /**
     * @return array
     */
    public function getQueueOptions();
}