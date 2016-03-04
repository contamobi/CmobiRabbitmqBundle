<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\Rpc\Exception\InvalidBodyAMQPMessageException;
use PhpAmqpLib\Message\AMQPMessage;

abstract class RpcBaseService implements RpcServiceInterface
{
    private $request;
    private $queueName;

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

    public function __construct(array $queueOptions, array $parameters = null)
    {
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
            $body = $this->buildResponse();

            if (!is_string($body) || is_null($body)) {
                throw new InvalidBodyAMQPMessageException('Invalid Body: Content should be string and not null.');
            }
            $message = new AMQPMessage(
                $body,
                ['correlation_id' => $request->get('correlation_id')]
            );

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
}