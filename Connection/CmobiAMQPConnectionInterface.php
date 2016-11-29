<?php

namespace Cmobi\RabbitmqBundle\Connection;


interface CmobiAMQPConnectionInterface
{
    /**
     * @return CmobiAMQPChannel
     */
    public function channel();
}