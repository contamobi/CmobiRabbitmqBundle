<?php

namespace Cmobi\RabbitmqBundle\Connection;

use CmobiRabbitmqBundle\Connection\CmobiAMQPChannel;
use PhpAmqpLib\Connection\AMQPLazyConnection;

class CmobiAMQPLazyConnection extends AMQPLazyConnection implements CmobiAMQPConnectionInterface
{
    /**
     * Fetches a channel object identified by the numeric channel_id, or
     * create that object if it doesn't already exist.
     *
     * @param string $channel_id
     * @return CmobiAMQPChannel
     */
    public function channel($channel_id = null)
    {
        if (isset($this->channels[$channel_id])) {
            return $this->channels[$channel_id];
        }

        $channel_id = $channel_id ? $channel_id : $this->get_free_channel_id();
        $ch = new CmobiAMQPChannel($this->connection, $channel_id);
        $this->channels[$channel_id] = $ch;

        return $ch;
    }
}