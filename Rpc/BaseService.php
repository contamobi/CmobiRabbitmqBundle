<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\Rpc\Exception\InvalidBodyAMQPMessageException;
use PhpAmqpLib\Message\AMQPMessage;

class BaseService implements RpcServiceInterface
{
    private $request;
    private $queueName;
    private $rpcHandler;

    /** @var array */
    protected $queueOptions = [
        'name'                  => null,
        'passive'               => false,
        'durable'               => true,
        'exclusive'             => false,
        'auto_delete'           => false, //Em caso de falha no serviço a filha se mantém para que outro processe.
        'nowait'                => false,
        'arguments'             => null,
        'ticket'                => null
    ];

    public function __construct(Handler $handler, array $queueOptions, array $parameters = null)
    {
        $this->rpcHandler = $handler;
        $this->queueName = $queueOptions['name'];
        $this->queueOptions = array_merge($this->queueOptions, $queueOptions);
    }

    /**
     * @return \Closure
     * @throws InvalidBodyAMQPMessageException
     */
    public function createCallback()
    {
        $callback = function (AMQPMessage $request) {

            $this->request = $request;
            $message = $this->getHandler()->handle($request);

            $request->delivery_info['channel']->basic_publish(
                $message,
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

    /**
     * @return array
     */
    public function getQueueOptions()
    {
        return $this->queueOptions;
    }

    /**
     * @return AMQPMessage
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Handler
     */
    public function getHandler()
    {
        return $this->rpcHandler;
    }
}