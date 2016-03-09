<?php

namespace Cmobi\RabbitmqBundle\Tests\Fixtures;

use Cmobi\RabbitmqBundle\Rpc\BaseService;

class RpcServiceMock extends BaseService
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