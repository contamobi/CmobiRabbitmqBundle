<?php

namespace Cmobi\RabbitmqBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RpcServicePass implements CompilerPassInterface
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
        $definition = new Definition(
            $this->serviceClass,
            [
                'handler' => $rpcHandler,
                'queueOptions' => $this->queueOptions,
                'parameters' => $this->params
            ]
        );
        $container->setDefinition($this->serviceName, $definition);
    }
}