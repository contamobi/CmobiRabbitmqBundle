<?php

namespace Cmobi\RabbitmqBundle\Tests\Pool;

use Cmobi\RabbitmqBundle\Pool\PoolManager;
use Cmobi\RabbitmqBundle\Pool\QueueJailed;
use Cmobi\RabbitmqBundle\Queue\QueueInterface;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;

class PoolManagerTest extends BaseTestCase
{
    public function testAddQueue()
    {
        $manager = new PoolManager();
        $queue = new QueueJailed($this->getQueueMock());

        $manager->addQueue($queue);

        $this->assertEquals(1, $manager->getQueues()->count());
    }

    public function testRemoveQueue()
    {
        $manager = new PoolManager();
        $queue = new QueueJailed($this->getQueueMock());
        $queue->start();
        $manager->addQueue($queue);

        $this->assertFalse($manager->removeQueue($queue));
    }

    public function testRemoveRunningQueue()
    {
        $manager = new PoolManager();
        $queue = new QueueJailed($this->getQueueMock());
        $manager->addQueue($queue);
        $manager->removeQueue($queue);

        $this->assertEquals(0, $manager->getQueues()->count());
    }

    /**
     * @return QueueInterface
     */
    protected function getQueueMock()
    {
        $queue = $this->getMockBuilder(QueueInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $queue;
    }
}