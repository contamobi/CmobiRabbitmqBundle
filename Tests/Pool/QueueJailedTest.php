<?php

namespace Cmobi\RabbitmqBundle\Tests\Pool;

use Cmobi\RabbitmqBundle\Pool\QueueJailed;
use Cmobi\RabbitmqBundle\Queue\QueueInterface;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;

class QueueJailedTest extends BaseTestCase
{
    public function testGetQueue()
    {
        $queueJailed = new QueueJailed($this->getQueueMock());

        $this->assertInstanceOf(QueueInterface::class, $queueJailed->getQueue());
    }

    /**
     * @return QueueInterface
     */
    protected function getQueueMock()
    {
        $queueMock = $this->getMockBuilder(QueueInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $queueMock;
    }
}