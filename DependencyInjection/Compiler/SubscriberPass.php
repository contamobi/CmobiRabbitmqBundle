<?php

namespace Cmobi\RabbitmqBundle\DependencyInjection\Compiler;

use Cmobi\RabbitmqBundle\Queue\Queue;
use Cmobi\RabbitmqBundle\Transport\Subscriber\ExchangeType;
use Cmobi\RabbitmqBundle\Transport\Subscriber\SubscriberQueueBag;
use Cmobi\RabbitmqBundle\Transport\Subscriber\SubscriberQueueCallback;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class SubscriberPass implements CompilerPassInterface
{
    private $exchangeName;
    private $exchangeType;
    private $queueName;
    private $connectionName;
    private $serviceName;
    private $basicQos;
    private $arguments;
    private $jobs;

    public function __construct(
        $exchangeName,
        $type = ExchangeType::FANOUT,
        $queueName = null,
        $connectionName,
        $serviceName,
        $basicQos,
        array $arguments,
        $jobs = 1
    )
    {
        $this->exchangeName = $exchangeName;
        $this->exchangeType = $type;
        $this->queueName = $queueName;
        $this->connectionName = $connectionName;
        $this->serviceName = $serviceName;
        $this->basicQos = $basicQos;
        $this->arguments = $arguments;
        $this->jobs = $jobs;
    }

    /**
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function process(ContainerBuilder $container)
    {
        for ($job = 0; $job < $this->jobs; $job++) {
            $definition = $this->buildDefinition($container);
            $definition->addTag('cmobi.subscriber');
            $jobName = sprintf('cmobi_rabbitmq.subscriber.%s_%s', $this->queueName, $job);
            $container->setDefinition($jobName, $definition);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @return Definition
     * @throws \Exception
     */
    protected function buildDefinition(ContainerBuilder $container)
    {
        $connection = $container->getDefinition('cmobi_rabbitmq.connection.manager');
        $logger = $container->getDefinition('cmobi_rabbitmq.logger');
        $serviceDefinition = $container->getDefinition($this->serviceName);
        $queueBagDefinition = new Definition(
            SubscriberQueueBag::class,
            [
                'exchangeName' => $this->exchangeName,
                'type' => $this->exchangeType,
                'queueName' => $this->queueName,
                'basicQos' => $this->basicQos,
                'arguments' => $this->arguments
            ]
        );
        $queueCallbackDefinition = new Definition(
            SubscriberQueueCallback::class,
            [
                'queueService' => $serviceDefinition
            ]
        );
        $definition = new Definition(
            Queue::class,
            [
                'connectionManager' => $connection,
                'queueBag' => $queueBagDefinition,
                'logger' => $logger,
                'connectionName' => $this->connectionName,
                'callback' => $queueCallbackDefinition
            ]
        );

        return $definition;
    }
}