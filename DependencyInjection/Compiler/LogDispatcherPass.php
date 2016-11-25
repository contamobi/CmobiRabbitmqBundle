<?php

namespace Cmobi\RabbitmqBundle\DependencyInjection\Compiler;

use Cmobi\RabbitmqBundle\Logger\LogDispatcher;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class LogDispatcherPass implements CompilerPassInterface
{
    private $path;
    public function __construct($path)
    {
        $this->path = $path;
    }
    public function process(ContainerBuilder $container)
    {
        $definition = new Definition(
            LogDispatcher::class,
            [
                'path' => $this->path
            ]
        );
        $container->setDefinition('cmobi_rabbitmq.logger', $definition);
    }
}