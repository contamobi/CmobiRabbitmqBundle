<?php

namespace Cmobi\RabbitmqBundle\Test\Transport\PubSub;

use Cmobi\RabbitmqBundle\Queue\QueueServiceInterface;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use Cmobi\RabbitmqBundle\Transport\PubSub\SubscriberQueueCallback;

class SubscriberQueueCallbackTest extends BaseTestCase
{
    public function testGetQueueService()
    {
        $rpcQueueCallback = new SubscriberQueueCallback($this->getQueueServiceMock());
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
