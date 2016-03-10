<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\Rpc\Exception\InvalidBodyAMQPMessageException;
use Cmobi\RabbitmqBundle\Rpc\Exception\JsonRpcInternalErrorException;
use Cmobi\RabbitmqBundle\Rpc\Response\JsonRpcResponse;
use Cmobi\RabbitmqBundle\Rpc\Response\RpcResponseCollectionInterface;
use Cmobi\RabbitmqBundle\Rpc\Response\RpcResponseInterface;
use PhpAmqpLib\Message\AMQPMessage;

class BaseService implements RpcServiceInterface
{
    private $queueName;
    private $rpcHandler;
    private $rpcMessager;

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

    public function __construct(Handler $handler, RpcMessager $messager, array $queueOptions, array $parameters = null)
    {
        $this->rpcHandler = $handler;
        $this->rpcMessager = $messager;
        $this->queueName = $queueOptions['name'];
        $this->queueOptions = array_merge($this->queueOptions, $queueOptions);
    }

    /**
     * @return \Closure
     * @throws InvalidBodyAMQPMessageException
     */
    public function createCallback()
    {
        $callback = function (AMQPMessage $message) {

            $responseCollection = $this->rpcMessager->getResponseCollection();
            $requestCollection = $this->rpcMessager->getRequestCollection();
            try {
                $this->rpcMessager->parseAMQPMessage($message);
                $this->getHandler()->handle($requestCollection, $responseCollection);
            } catch (\Exception $e) {
                $exception = new JsonRpcInternalErrorException();
                $response = new JsonRpcResponse([], $exception);
                $responseCollection->add($response);
            }
            $messageResponse = $this->buildResponseMessage($responseCollection, $message);

            $message->delivery_info['channel']->basic_publish(
                $messageResponse,
                '',
                $message->get('reply_to')
            );
            $message->delivery_info['channel']->basic_ack(
                $message->delivery_info['delivery_tag']
            );
        };

        return $callback;
    }

    public function buildResponseMessage(RpcResponseCollectionInterface $response, AMQPMessage $requestMessage)
    {
        $amqpResponse = new AMQPMessage(
            (string)$response,
            ['correlation_id' => $requestMessage->get('correlation_id')]
        );

        return $amqpResponse;
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
     * @return Handler
     */
    public function getHandler()
    {
        return $this->rpcHandler;
    }

    /**
     * @return RpcMessager
     */
    public function getMessager()
    {
        return $this->rpcMessager;
    }
}