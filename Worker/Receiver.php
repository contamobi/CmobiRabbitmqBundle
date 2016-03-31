<?php

namespace Cmobi\RabbitmqBundle\Worker;

use AMQPChannel;
use Cmobi\RabbitmqBundle\MessageBroker\ServerInterface;
use Cmobi\RabbitmqBundle\MessageBroker\ServiceInterface;
use Cmobi\RabbitmqBundle\Worker\Exception\InvalidWorkerServiceException;
use Cmobi\RabbitmqBundle\Worker\Exception\NotFoundWorkerException;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Receiver implements ServerInterface
{
    use ContainerAwareTrait;

    private $workers;
    private $connection;
    private $channel;

    public function __construct(array $workers, AMQPStreamConnection $connection = null)
    {
        if (!$workers) {
            throw new NotFoundWorkerException('Any worker serivce found.');
        }
        $this->workers = $workers;

        if (!is_null($connection)) {
            $this->connection = $connection;
        }
    }

    /**
     * @param $queue
     * @param ServiceInterface $serviceCallback
     * @param bool|false $passive
     * @param bool|true $durable
     * @param bool|false $exclusive
     * @param bool|true $auto_delete
     * @param bool|false $nowait
     * @param null $arguments
     * @param null $ticket
     */
    public function publishWorker(
        $queue,
        ServiceInterface $serviceCallback,
        $passive = false,
        $durable = true,
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

    /**
     * @throws InvalidWorkerServiceException
     */
    public function run()
    {
        foreach ($this->workers as $workerName) {
            $service = $this->getContainer()->get($workerName);

            if (!$service instanceof ServiceInterface) {
                throw new InvalidWorkerServiceException(
                    'Failed start WorkerServer: %s is not instance of ServiceInterface'. $workerName
                );
            }
            list(
                $name, $passive, $durable, $exclusive, $auto_delete, $nowait, $arguments, $ticket
                ) = array_values($service->getQueueOptions());
            $this->publishWorker(
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