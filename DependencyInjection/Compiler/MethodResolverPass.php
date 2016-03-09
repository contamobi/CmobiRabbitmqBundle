<?php

namespace Cmobi\RabbitmqBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MethodResolverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('rpc_routing.resolver')) {
            return;
        }

        $definition = $container->getDefinition('rpc_routing.resolver');

        foreach ($container->findTaggedServiceIds('routing.loader') as $id => $attributes) {
            $definition->addMethodCall('addLoader', array(new Reference($id)));
        }
    }
}