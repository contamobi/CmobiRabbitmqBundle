<?php

namespace Cmobi\RabbitmqBundle\DependencyInjection;

use Cmobi\RabbitmqBundle\DependencyInjection\Compiler\RpcServerPass;
use Cmobi\RabbitmqBundle\DependencyInjection\Compiler\SubscriberPass;
use Cmobi\RabbitmqBundle\DependencyInjection\Compiler\WorkerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CmobiRabbitmqExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $fileLocator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader = new YamlFileLoader($container, $fileLocator);
        $loader->load('rabbitmq.yml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        $this->loadConnections($container, $configs[0]);
        $this->loadRpcServers($container, $config);
        $this->loadWorkers($container, $config);

        /* Compile and lock container */
        $container->compile();
    }

    protected function loadConnections(ContainerBuilder $container, array $configs)
    {
        $factories = [];

        foreach ($configs['connections'] as $name => $connection) {
            $connectionClass = '%cmobi_rabbitmq.connection.class%';

            if ($connection['lazy']) {
                $connectionClass = '%cmobi_rabbitmq.lazy.connection.class%';
            }
            $definition = new Definition(
                '%cmobi_rabbitmq.connection.factory.class%',
                [
                    $connectionClass,
                    $connection,
                ]
            );
            $factoryName = sprintf('cmobi_rabbitmq.connection.factory.%s', $name);
            $container->setDefinition($factoryName, $definition);
            $factories[$name] = $factoryName;
        }
        $container->setParameter('cmobi_rabbitmq.connection.factories', $factories);
    }

    public function loadRpcServers(ContainerBuilder $container, array $configs)
    {
        foreach ($configs['rpc_servers'] as $server) {

            $container->addCompilerPass(new RpcServerPass(
                $server['queue']['name'],
                $server['queue']['connection'],
                $server['service'],
                $server['queue']['basic_qos'],
                $server['queue']['durable'],
                $server['queue']['auto_delete'],
                $server['queue']['arguments']
            ));
        }
    }

    public function loadWorkers(ContainerBuilder $container, array $configs)
    {
        foreach ($configs['workers'] as $worker) {
            $container->addCompilerPass(new WorkerPass(
                $worker['queue']['name'],
                $worker['queue']['connection'],
                $worker['service'],
                $worker['queue']['basic_qos'],
                $worker['queue']['arguments']
            ));
        }
    }

    public function loadSubscribers(ContainerBuilder $container, array $configs)
    {
        foreach ($configs['subscribers'] as $subscriber) {
            $container->addCompilerPass(new SubscriberPass(
                $subscriber['queue']['exchange'],
                $subscriber['queue']['exchange_type'],
                $subscriber['queue']['name'],
                $subscriber['queue']['connection'],
                $subscriber['service'],
                $subscriber['queue']['basic_qos'],
                $subscriber['queue']['arguments']
            ));
        }
    }
}
