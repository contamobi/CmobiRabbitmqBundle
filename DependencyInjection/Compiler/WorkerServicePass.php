<?php

namespace Cmobi\RabbitmqBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class WorkerServicePass implements CompilerPassInterface
{
    private $serviceName;
    private $serviceClass;
    private $queueOptions;
    private $params;

    public function __construct($serviceName, $serviceClass, array $queueOptions, array $params = [])
    {
        $this->serviceName = $serviceName;
        $this->serviceClass = $serviceClass;
        $this->queueOptions = $queueOptions;
        $this->params = $params;
    }

    public function process(ContainerBuilder $container)
    {
        $rpcHandler = $container->getDefinition('cmobi_rabbitmq.rpc.handler');
        $rpcMessager = $container->getDefinition('cmobi_rabbitmq.rpc.messager');
        $definition = new Definition(
            $this->serviceClass,
            [
                'handler' => $rpcHandler,
                'messager' => $rpcMessager,
                'queueOptions' => $this->queueOptions,
                'parameters' => $this->params
            ]
        );
        $container->setDefinition($this->serviceName, $definition);
    }
}