<?php

namespace Cmobi\RabbitmqBundle\Pool;

use Cmobi\RabbitmqBundle\Queue\QueueJailed;

class PoolManager
{
    private $pools;

    public function __construct()
    {
        $this->pools = new \SplObjectStorage();
    }

    /**
     * @param QueueJailed $queue
     * @param int $size
     * @return $this
     */
    public function publishQueue(QueueJailed $queue, int $size)
    {
        $pool = new \Pool($size, Autoloader::class, ["vendor/autoload.php"]);
        $pool->submit($queue);

        if (! $this->getPools()->contains($pool)) {
            $this->getPools()->attach($pool);
        }

        $pool->shutdown();

        return $this;
    }

    /**
     * @return \SplObjectStorage
     */
    public function getPools()
    {
        return $this->pools;
    }
}