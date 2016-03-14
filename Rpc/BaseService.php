<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\Rpc\Exception\InvalidBodyAMQPMessageException;
use Cmobi\RabbitmqBundle\Rpc\Exception\RpcInternalErrorException;
use Cmobi\RabbitmqBundle\Rpc\Exception\RpcInvalidResponseException;
use Cmobi\RabbitmqBundle\Rpc\Response\RpcResponse;
use Cmobi\RabbitmqBundle\Rpc\Response\RpcResponseCollection;
use Cmobi\RabbitmqBundle\Rpc\Response\RpcResponseCollectionInterface;
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
            try {
                $requestCollection = $this->rpcMessager->parseAMQPMessage($message);
                $responseCollection = $this->getHandler()->handle($requestCollection);
            } catch (\Exception $e) {
                $responseCollection = new RpcResponseCollection();
                $exception = new RpcInternalErrorException();
                $response = new RpcResponse(null, [], $exception);
                $responseCollection->add($response);
            }
            $messageResponse = $this->buildResponseMessage($responseCollection, $message);
            $this->publish($message, $messageResponse);
        };

        return $callback;
    }

    /**
     * @param RpcResponseCollectionInterface $responses
     * @param AMQPMessage $requestMessage
     * @return AMQPMessage
     * @throws RpcInvalidResponseException
     */
    public function buildResponseMessage(RpcResponseCollectionInterface $responses, AMQPMessage $requestMessage)
    {
        $rpcResponse = [];
        /**
         * @var RpcResponse $response
         */
        foreach ($responses->all() as $response) {

            if (is_null($response->id)) {
                $response->id = $requestMessage->get('correlation_id');
            }
            $rpcResponse[] = $response->toArray();
        }
        try {
            $rpcResponse = json_encode($rpcResponse);
        } catch (\Exception $e) {
            throw new RpcInvalidResponseException($e);
        }
        $amqpResponse = new AMQPMessage(
            (string)$rpcResponse,
            ['correlation_id' => $requestMessage->get('correlation_id')]
        );

        return $amqpResponse;
    }

    /**
     * @param AMQPMessage $message
     * @param $content
     */
    public function publish(AMQPMessage $message, $content)
    {
        $message->delivery_info['channel']->basic_publish(
            $content,
            '',
            $message->get('reply_to')
        );
        $message->delivery_info['channel']->basic_ack(
            $message->delivery_info['delivery_tag']
        );
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