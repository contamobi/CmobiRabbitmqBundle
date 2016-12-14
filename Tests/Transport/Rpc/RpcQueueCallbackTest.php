<?php

namespace Cmobi\RabbitmqBundle\Test\Rpc;

use Cmobi\RabbitmqBundle\Queue\QueueServiceInterface;
use Cmobi\RabbitmqBundle\Transport\Rpc\RpcQueueCallback;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;

class RpcQueueCallbackTest extends BaseTestCase
{
    public function testGetQueueService()
    {
        $rpcQueueCallback = new RpcQueueCallback($this->getQueueServiceMock());
        $queueService = $rpcQueueCallback->getQueueService();

        $this->assertInstanceOf(QueueServiceInterface::class, $queueService);
    }

    /**
     * @return QueueServiceInterface
     */
    protected function getQueueServiceMock()
    {
        $queueServiceMock = $this->getMockBuilder(QueueServiceInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $queueServiceMock->method('handle')
            ->willReturn('');

        return $queueServiceMock;
    }
}