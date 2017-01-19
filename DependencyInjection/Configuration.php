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

        $rootNode->fixXmlConfig('rpc_server')
            ->children()
                ->arrayNode('rpc_servers')
                    ->useAttributeAsKey('key')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('queue')
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->scalarNode('connection')->defaultValue('default')->end()
                                    ->scalarNode('basic_qos')->defaultValue(1)->end()
                                    ->booleanNode('durable')->defaultTrue()->end()
                                    ->booleanNode('auto_delete')->defaultFalse()->end()
                                    ->variableNode('arguments')->defaultValue([])->end()
                                ->end()
                            ->end()
                            ->scalarNode('service')->defaultValue('cmobi_rabbitmq.message.handler')->end()
                            ->scalarNode('jobs')->defaultValue(1)->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        $rootNode->fixXmlConfig('worker')
            ->children()
                ->arrayNode('workers')
                    ->useAttributeAsKey('key')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('queue')
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->scalarNode('connection')->defaultValue('default')->end()
                                    ->scalarNode('basic_qos')->defaultValue(1)->end()
                                    ->variableNode('arguments')->defaultValue([])->end()
                                ->end()
                            ->end()
                            ->scalarNode('service')->defaultValue('cmobi_rabbitmq.message.handler')->end()
                            ->scalarNode('jobs')->defaultValue(1)->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        $rootNode->fixXmlConfig('subscriber')
            ->children()
                ->arrayNode('subscribers')
                    ->useAttributeAsKey('key')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('queue')
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->scalarNode('exchange')->end()
                                    ->scalarNode('connection')->defaultValue('default')->end()
                                    ->scalarNode('basic_qos')->defaultValue(1)->end()
                                    ->scalarNode('exchange_type')->defaultValue('topic')->end()
                                    ->variableNode('arguments')->defaultValue([])->end()
                                ->end()
                            ->end()
                            ->scalarNode('service')->defaultValue('cmobi_rabbitmq.message.handler')->end()
                            ->scalarNode('jobs')->defaultValue(1)->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $tree;
    }
}
