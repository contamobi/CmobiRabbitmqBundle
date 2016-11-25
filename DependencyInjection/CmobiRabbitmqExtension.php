<?php

namespace Cmobi\RabbitmqBundle\DependencyInjection;

use Cmobi\RabbitmqBundle\DependencyInjection\Compiler\ConfigCachePass;
use Cmobi\RabbitmqBundle\DependencyInjection\Compiler\LogDispatcherPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CmobiRabbitmqExtension extends Extension
{
    private $container;
    private $config;

    public function load(array $configs, ContainerBuilder $container)
    {
        $fileLocator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader = new YamlFileLoader($container, $fileLocator);
        $loader->load('rabbitmq.yml');

        $configuration = $this->getConfiguration($configs, $container);
        $this->config = $this->processConfiguration($configuration, $configs);
        $this->container = $container;
        $this->registerLogger($configs[0]['log_path']);
        $this->loadConnections();
        $container->setParameter('cmobi_rabbitmq.basic_qos', $configs[0]['basic_qos']);

        /* Compile and lock container */
        $container->compile();
    }

    protected function loadConnections()
    {
        $factories = [];

        foreach ($this->config['connections'] as $name => $connection) {

            $connectionClass = '%cmobi_rabbitmq.connection.class%';

            if ($connection['lazy']) {
                $connectionClass = '%cmobi_rabbitmq.lazy.connection.class%';
            }
            $definition = new Definition(
                '%cmobi_rabbitmq.connection.factory.class%',
                [
                    $connectionClass,
                    $connection
                ]
            );
            $factoryName = sprintf('cmobi_rabbitmq.connection.factory.%s', $name);
            $this->getContainer()->setDefinition($factoryName, $definition);
            $factories[$name] = $factoryName;
        }
        $this->getContainer()->setParameter('cmobi_rabbitmq.connection.factories', $factories);
    }

    /**
     * @param $path
     */
    public function registerLogger($path)
    {
        $logDispatcherPass = new LogDispatcherPass($path);
        $this->getContainer()->addCompilerPass($logDispatcherPass);
    }

    /**
     * @return ContainerBuilder
     */
    private function getContainer()
    {
        return $this->container;
    }
}