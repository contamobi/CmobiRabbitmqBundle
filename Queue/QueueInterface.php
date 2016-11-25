<?php
declare(strict_types=1);

namespace Cmobi\RabbitmqBundle\Queue;

interface QueueInterface
{
    /**
     * Declare and start queue in broker
     */
    public function start();

    /**
     * @return int
     */
    public function getBasicQos() : int;
}