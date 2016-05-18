<?php

namespace Cmobi\RabbitmqBundle\DependencyInjection\Compiler;

use Cmobi\RabbitmqBundle\Rpc\RpcServer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RpcServerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = new Definition(RpcServer::class, [
            'rpcServices' => $container->getParameter('cmobi_rabbitmq.rpc_services')
        ]);
        $definition->addMethodCall('setContainer', [new Reference('service_container')]);
        $definition->addMethodCall('buildChannel');

        if ($container->has('logger')) {
            $definition->addMethodCall('setLogger', [new Reference('logger')]);
        }
        $container->setDefinition('cmobi_rabbitmq.rpc_server', $definition);
    }
}