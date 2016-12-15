<?php

namespace Cmobi\RabbitmqBundle\Pool;

use Cmobi\RabbitmqBundle\Queue\QueueInterface;

class QueueJailed extends \Thread
{
    private $queue;

    public function __construct(QueueInterface $queue)
    {
        $this->queue = $queue;
    }

    public function run()
    {
        $this->getQueue()->start();
    }

    /**
     * @return QueueInterface
     */
    public function getQueue()
    {
        return $this->queue;
    }
}