<?php

namespace Cmobi\RabbitmqBundle\Queue;

use Cmobi\RabbitmqBundle\Connection\CmobiAMQPChannel;
use Cmobi\RabbitmqBundle\Connection\CmobiAMQPConnection;
use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Connection\Exception\InvalidAMQPChannelException;
use Cmobi\RabbitmqBundle\Domain\Model\RpcQueueServer;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Queue implements QueueInterface
{
    private $connectionManager;
    private $connection;
    private $connectionName;
    private $channel;
    private $queueBag;
    private $callback;
    private $logger;
    private $container;

    public function __construct(
        ConnectionManager $connectionManager,
        QueueBagInterface $queueBag,
        LoggerInterface $logger,
        $connectionName = 'default',
        QueueCallbackInterface $callback = null
    )
    {
        $this->connectionManager = $connectionManager;
        $this->connectionName = $connectionName;
        $this->connection = $this->getConnectionManager()->getConnection($connectionName);
        $this->queueBag = $queueBag;
        $this->logger = $logger;
        $this->callback = $callback;
    }

    /**
     * @return CmobiAMQPChannel
     *
     * @throws InvalidAMQPChannelException
     */
    protected function getChannel()
    {
        if ($this->channel instanceof CmobiAMQPChannel) {
            return $this->channel;
        }
        $this->channel = $this->getConnection()->channel();

        if (!$this->channel instanceof CmobiAMQPChannel) {
            throw new InvalidAMQPChannelException('Failed get AMQPChannel');
        }

        return $this->channel;
    }

    protected function createQueue()
    {
        $queueBag = $this->getQueuebag();

        $this->getChannel()->basic_qos(null, $queueBag->getBasicQos(), null);

        if ($queueBag->getExchangeDeclare()) {
            $this->getChannel()->exchangeDeclare($queueBag->getExchangeDeclare());
            list($queueName) = $this->getChannel()->queueDeclare($queueBag->getQueueDeclare());
            $this->getChannel()->queue_bind($queueName, $queueBag->getExchange());
        } else {
            $this->getChannel()->queueDeclare($queueBag->getQueueDeclare());
        }
        $this->getChannel()->basicConsume($queueBag->getQueueConsume(), $this->getCallback()->toClosure());
    }

    /**
     * Declare and start queue in broker.
     * @param string|null $serviceName Service process name
     */
    public function start($serviceName=null)
    {
        $this->createQueue();

        while (count($this->getChannel()->callbacks)) {
            try {
                dump('Waitng...');

                $rpcQueueServer = null;
                if($this->getContainer() instanceof ContainerInterface) {
                    $this->getChannel()->setHealthCheckService($this->getContainer()->get('cmobi_rabbitmq.healthcheck'));
                    $rpcQueueServer = new RpcQueueServer();
                    $rpcQueueServer->setProcess($serviceName)
                                    ->setQueue($this->queueBag->getQueue());
                }
                $this->getChannel()->wait(null,null,null,$rpcQueueServer);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                $this->forceReconnect();

                continue;
            }
        }
        $connection = $this->getChannel()->getConnection();
        $this->getChannel()->close();
        $connection->close();
    }

    /**
     * @return QueueBagInterface
     */
    public function getQueuebag()
    {
        return $this->queueBag;
    }

    /**
     * @param QueueCallbackInterface $callback
     */
    public function setCallback(QueueCallbackInterface $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return QueueCallbackInterface
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return ConnectionManager
     */
    public function getConnectionManager()
    {
        return $this->connectionManager;
    }

    /**
     * @return CmobiAMQPConnection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Retry connect to message broker until it can.
     */
    /**
     * @param CmobiAMQPConnection|null $connection
     *
     * @return CmobiAMQPChannel
     */
    public function forceReconnect(CmobiAMQPConnection $connection = null)
    {
        do {
            try {
                $failed = false;
                $this->logger->warning('forceReconnect() - trying connect...');
                $this->connection = $this->getConnectionManager()->getConnection($this->connectionName);
                $this->channel = $this->getConnection()->channel();
                $this->createQueue();
            } catch (\Exception $e) {
                $failed = true;
                sleep(3);
                $this->logger->error('forceReconnect() - '.$e->getMessage());
            }
        } while ($failed);
        $this->logger->warning('forceReconnect() - connected!');

        return $this->channel;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container
     * @return $this
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
    }
}
