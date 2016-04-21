<?php

namespace Cmobi\RabbitmqBundle\DependencyInjection;

use Cmobi\RabbitmqBundle\Rpc\BaseService;
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
        $rootNode
            ->children()
                ->arrayNode('router')
                    ->info('router configuration')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('resource')->isRequired()->end()
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
                                         ->booleanNode('passive')->defaultFalse()->end()
                                         ->booleanNode('durable')->defaultTrue()->end()
                                         ->booleanNode('exclusive')->defaultFalse()->end()
                                         ->booleanNode('auto_delete')->defaultFalse()->end()
                                         ->booleanNode('nowait')->defaultFalse()->end()
                                         ->variableNode('arguments')->defaultNull()->end()
                                         ->scalarNode('ticket')->defaultNull()->end()
                                         ->arrayNode('routing_keys')
                                            ->prototype('scalar')->end()
                                         ->end()
                                     ->end()
                            ->end()
                            ->scalarNode('class')->defaultValue(BaseService::class)->end()
                            ->arrayNode('arguments')->canBeDisabled()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $tree;
    }
}