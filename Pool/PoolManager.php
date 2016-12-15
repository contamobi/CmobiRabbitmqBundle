<?php

namespace Cmobi\RabbitmqBundle\Pool;

class PoolManager
{
    private $queues;

    public function __construct()
    {
        $this->queues = new \SplObjectStorage();
    }

    /**
     * @param QueueJailed $queue
     * @return $this
     */
    public function addQueue(QueueJailed $queue)
    {
        if (! $this->getQueues()->contains($queue)) {
            $this->getQueues()->attach($queue);
        }

        return $this;
    }

    /**
     * @param QueueJailed $queue
     * @return $this|bool
     */
    public function removeQueue(QueueJailed $queue)
    {
        if ($queue->isRunning()) {
            return false;
        }

        if (! $this->getQueues()->contains($queue)) {
            return false;
        }
        $this->getQueues()->detach($queue);

        return $this;
    }

    public function start()
    {
        foreach ($this->getQueues() as $queue) {

            if ($queue instanceof QueueJailed) {
                $queue->start();
            }
        }
    }

    public function stop()
    {
        foreach ($this->getQueues() as $queue) {

            if ($queue instanceof QueueJailed) {
                $queue->kill();
            }
        }
    }

    /**
     * @return \SplObjectStorage
     */
    public function getQueues()
    {
        return $this->queues;
    }
}