<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\Rpc\Exception\InvalidRpcServerClassException;
use Cmobi\RabbitmqBundle\Rpc\Exception\NotFoundRpcServiceException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class RpcServer
{
    use ContainerAwareTrait;

    private $rpcServices;
    private $connection;
    private $channel;

    public function __construct(array $rpcServices, AMQPStreamConnection $connection = null)
    {
        if (!$rpcServices) {
            throw new NotFoundRpcServiceException('no rpc services found.');
        }
        $this->rpcServices = $rpcServices;

        if (!is_null($connection)) {
            $this->connection = $connection;
        }
    }

    /**
     * @param $queue
     * @param RpcServiceInterface $serviceCallback
     * @param bool|false $passive
     * @param bool|false $durable
     * @param bool|false $exclusive
     * @param bool|true $auto_delete
     * @param bool|false $nowait
     * @param null $arguments
     * @param null $ticket
     */
    public function pushMessage(
        $queue,
        RpcServiceInterface $serviceCallback,
        $passive = false,
        $durable = false,
        $exclusive = false,
        $auto_delete = true,
        $nowait = false,
        $arguments = null,
        $ticket = null
    )
    {
        $this->getChannel()->queue_declare(
            $queue, $passive, $durable, $exclusive, $auto_delete, $nowait, $arguments, $ticket
        );
        $this->getChannel()->basic_qos(null, 1, null);
        $this->getChannel()->basic_consume($queue, '', false, false, $exclusive, $nowait, $serviceCallback->createCallback());
    }

    public function run()
    {
        foreach ($this->rpcServices as $serviceName) {
            $service = $this->getContainer()->get($serviceName);

            if (!$service instanceof RpcServiceInterface) {
                throw new InvalidRpcServerClassException(
                    'Failed start RpcServer: %s is not instance of RpcServiceInterface'. $serviceName
                );
            }
            list(
                $name, $passive, $durable, $exclusive, $auto_delete, $nowait, $arguments, $ticket
                ) = array_values($service->getQueueOptions());
            $this->pushMessage(
                $service->getQueueName(),
                $service,
                $passive,
                $durable,
                $exclusive,
                $auto_delete,
                $nowait,
                $arguments,
                $ticket
            );
        }

        while(count($this->getChannel()->callbacks)) {
            $this->getChannel()->wait();
        }

        $this->getChannel()->close();
        $this->getConnection()->close();
    }

    /**
     * @throws \Cmobi\RabbitmqBundle\Amqp\Exception\NotFoundAMQPConnectionFactoryException
     */
    public function buildChannel()
    {
        if (!$this->connection instanceof AMQPStreamConnection) {
            $connectionManager = $this->getContainer()->get('cmobi_rabbitmq.connection.manager');
            $this->connection = $connectionManager->getConnection();
        }
        $this->channel = $this->connection->channel();
    }

    /**
     * @return AMQPStreamConnection
     */
    protected function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return AMQPChannel
     */
    protected function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }
}