<?php

namespace Cmobi\RabbitmqBundle\Connection;

use CmobiRabbitmqBundle\Connection\CmobiAMQPChannel;

interface CmobiAMQPConnectionInterface
{
    /**
     * @return CmobiAMQPChannel
     */
    public function channel();
}