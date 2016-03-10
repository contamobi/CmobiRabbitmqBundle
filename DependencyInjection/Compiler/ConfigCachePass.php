<?php


namespace Cmobi\RabbitmqBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConfigCachePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $resourceCheckers = array();

        foreach ($container->findTaggedServiceIds('cmobi_rabbitmq.config_cache.resource_checker') as $id => $tags) {
            $priority = isset($tags[0]['priority']) ? $tags[0]['priority'] : 0;
            $resourceCheckers[$priority][] = new Reference($id);
        }

        if (empty($resourceCheckers)) {
            return;
        }

        // sort by priority and flatten
        krsort($resourceCheckers);
        $resourceCheckers = call_user_func_array('array_merge', $resourceCheckers);

        $container->getDefinition('cmobi_rabbitmq.config_cache_factory')->replaceArgument(0, $resourceCheckers);
    }
}
