<?php

namespace Cmobi\RabbitmqBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $tree = new TreeBuilder();
        $rootNode = $tree->root('cmobi_rabbitmq');
        $rootNode
            ->children()
                ->scalarNode('basic_qos')->defaultValue(1)->end()
            ->end();
        $rootNode
            ->children()
                ->scalarNode('log_path')->end()
            ->end();
        $rootNode->fixXmlConfig('connection')
            ->children()
                ->arrayNode('connections')
                    ->useAttributeAsKey('key')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('host')->defaultValue('localhost')->end()
                            ->scalarNode('port')->defaultValue(5672)->end()
                            ->scalarNode('user')->defaultValue('guest')->end()
                            ->scalarNode('password')->defaultValue('guest')->end()
                            ->scalarNode('vhost')->defaultValue('/')->end()
                            ->booleanNode('lazy')->defaultFalse()->end()
                            ->scalarNode('connection_timeout')->defaultValue(3)->end()
                            ->scalarNode('read_write_timeout')->defaultValue(3)->end()
                            ->arrayNode('ssl_context')
                                ->useAttributeAsKey('key')
                                ->canBeUnset()
                                ->prototype('variable')->end()
                            ->end()
                            ->booleanNode('keepalive')->defaultFalse()->info('requires php-amqplib v2.4.1+ and PHP5.4+')->end()
                            ->scalarNode('heartbeat')->defaultValue(0)->info('requires php-amqplib v2.4.1+')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $tree;
    }
}
