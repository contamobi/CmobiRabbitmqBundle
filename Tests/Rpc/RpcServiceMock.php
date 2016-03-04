<?php

namespace Cmobi\RabbitmqBundle\Tests\Rpc;

use Cmobi\RabbitmqBundle\Rpc\RpcBaseService;

class RpcServiceMock extends RpcBaseService
{
    /**
     * @return array
     */
    public function getQueueOptions()
    {
        return $this->queueOptions;
    }

    /**
     * Build response for rpc server
     *
     * @return string
     */
    protected function buildResponse()
    {
        return 'RpcServiceMock';
    }
}