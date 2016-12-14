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
     * @param $callback
     * @return mixed|string
     */
    public function basicConsume(array $params, $callback)
    {
        list (
            $queue,
            $consumer_tag,
            $no_local,
            $no_ack,
            $exclusive,
            $nowait,
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

    /**
     * @param array $params
     * @return mixed|null
     */
    public function exchangeDeclare(array $params)
    {
        list (
        $exchange,
        $type,
        $passive,
        $durable,
        $autoDelete,
        $internal,
        $nowait,
        $arguments,
        $ticket
        ) = $params;

        return parent::exchange_declare(
            $exchange,
            $type,
            $passive,
            $durable,
            $autoDelete,
            $internal,
            $nowait,
            $arguments,
            $ticket
        );
    }
}