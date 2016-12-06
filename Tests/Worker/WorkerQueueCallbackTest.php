<?php

namespace Cmobi\RabbitmqBundle\Test\Rpc;

use Cmobi\RabbitmqBundle\Queue\QueueServiceInterface;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use Cmobi\RabbitmqBundle\Worker\WorkerQueueCallback;

class WorkerQueueCallbackTest extends BaseTestCase
{
    public function testGetQueueService()
    {
        $rpcQueueCallback = new WorkerQueueCallback($this->getQueueServiceMock());
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