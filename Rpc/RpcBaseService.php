<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use PhpAmqpLib\Message\AMQPMessage;

abstract class RpcBaseService implements RpcServiceInterface
{
    private $queueName;
    private $content;

    /** @var array */
    protected $queueOptions = [
        'name'                  => null,
        'passive'               => false,
        'durable'               => false,
        'exclusive'             => false,
        'auto_delete'           => true,
        'nowait'                => false,
        'arguments'             => null,
        'ticket'                => null
    ];

    public function __construct(array $queueOptions, array $parameters = null)
    {
        $this->queueName = $queueOptions['name'];
        $this->queueOptions = array_merge($this->queueOptions, $queueOptions);
    }


    /**
     * Should be Override
     * @return \Closure
     */
    public function createCallback()
    {
        $callback = function (AMQPMessage $request) {
            $this->content = new AMQPMessage(
                $request->body,
                ['correlation_id' => $request->get('correlation_id')]
            );

            $request->delivery_info['channel']->basic_publish(
                $this->content,
                '',
                $request->get('reply_to')
            );
            $request->delivery_info['channel']->basic_ack(
                $request->delivery_info['delivery_tag']
            );
        };

        return $callback;
    }

    public function getQueueName()
    {
        return $this->queueName;
    }
}