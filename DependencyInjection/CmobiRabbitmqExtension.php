<?php

namespace Cmobi\RabbitmqBundle\DependencyInjection;

use Cmobi\RabbitmqBundle\DependencyInjection\Compiler\RpcServicePass;
use Cmobi\RabbitmqBundle\Rpc\Exception\InvalidRpcServerClassException;
use Cmobi\RabbitmqBundle\Rpc\RpcServiceInterface;
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
        $this->registerRouterConfiguration($configs[0]['router']);
        $this->loadConnections();
        $this->loadRpcServers();
    }

    protected function loadConnections()
    {
        $connections = [];

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
            $connections[$name] = $factoryName;
        }
        $this->getContainer()->setParameter('cmobi_rabbitmq.connections', $connections);
    }

    public function loadRpcServers()
    {
        $rpcServers = [];

        foreach ($this->config['rpc_servers'] as $server => $params) {
            $serviceClass = $params['class'];

            if (!is_subclass_of($serviceClass, RpcServiceInterface::class)) {
                throw new InvalidRpcServerClassException(
                    sprintf('server (%s) is not class of RpcBaseService', $server)
                );
            }

            if (!isset($params['arguments'])) {
                $params['arguments'] = [];
            }
            $serviceName = sprintf('cmobi_rabbitmq.rpc_service.%s', $server);
            $this->getContainer()->addCompilerPass(
                new RpcServicePass($serviceName, $serviceClass, $params['queue'], $params['arguments'])
            );
            $rpcServers[$server] = $serviceName;
        }
        $this->getContainer()->setParameter('cmobi_rabbitmq.rpc_services', $rpcServers);
    }

    public function registerRouterConfiguration($resource)
    {
        $this->getContainer()->setParameter('cmobi_rabbitmq.router_rpc.resource', $resource);
        $this->getContainer()->setParameter(
            'method.cache_class_prefix',
            $this->getContainer()->getParameter('kernel.name')
            . ucfirst($this->getContainer()->getParameter('kernel.environment'))
        );
        $this->addClassesToCompile([
            'Cmobi\\RabbitmqBundle\\Routing\\Matcher\\MethodMatcher',
            $this->getContainer()->findDefinition('cmobi_rabbitmq.router_rpc')->getClass(),
        ]);
    }

    /**
     * @return ContainerBuilder
     */
    private function getContainer()
    {
        return $this->container;
    }
}