<?php

namespace Cmobi\RabbitmqBundle\Connection;

use PhpAmqpLib\Channel\AMQPChannel;

class CmobiAMQPChannel extends AMQPChannel
{
    /**
     * @param array $params
     * @return mixed|null
     */
    public function queueDeclare(array $params)
    {
        list (
            $queue,
            $passive,
            $durable,
            $exclusive,
            $auto_delete,
            $nowait,
            $arguments,
            $ticket
            ) = $params;

        return parent::queue_declare(
            $queue,
            $passive,
            $durable,
            $exclusive,
            $auto_delete,
            $nowait,
            $arguments,
            $ticket
        );
    }

    /**
     * @param array $params
     * @param \Closure $callback
     * @return mixed|string
     */
    public function basicConsume(array $params, \Closure $callback)
    {
        list (
            $queue,
            $consumer_tag,
            $no_local,
            $no_ack,
            $exclusive,
            $nowait,
            $callback,
            $ticket,
            $arguments
            ) = $params;

        return parent::basic_consume(
            $queue,
            $consumer_tag,
            $no_local,
            $no_ack,
            $exclusive,
            $nowait,
            $callback,
            $ticket,
            $arguments
        );
    }
}