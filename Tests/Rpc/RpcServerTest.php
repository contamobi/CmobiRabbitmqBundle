<?php

namespace Cmobi\RabbitmqBundle\Tests\Rpc;

use Cmobi\RabbitmqBundle\Rpc\RpcServer;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;

class RpcServerTest extends BaseTestCase
{
    public function testServiceExists()
    {
        $service = $this->getContainer()->get('cmobi_rabbitmq.rpc_server');
        $this->assertInstanceOf(RpcServer::class, $service);
    }
}