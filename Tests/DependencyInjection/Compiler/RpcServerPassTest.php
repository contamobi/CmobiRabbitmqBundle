<?php

namespace Cmobi\RabbitmqBundle\Tests\DependencyInjection\Compiler;

use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\DependencyInjection\CmobiRabbitmqExtension;
use Cmobi\RabbitmqBundle\DependencyInjection\Compiler\RpcServerPass;
use Cmobi\RabbitmqBundle\Queue\BaseQueueService;
use Cmobi\RabbitmqBundle\Queue\QueueServiceInterface;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RpcServerPassTest extends BaseTestCase
{
    public function testProcess()
    {
        $container = new ContainerBuilder();
        $this->declareServiceMock($container);
        $container->register('cmobi_rabbitmq.message.handler', BaseQueueService::class)
            ->setPublic(false);
        $container->register('cmobi_rabbitmq.connection.manager', ConnectionManager::class)
            ->setPublic(false);

        $this->process($container);

        $this->assertTrue($container->hasDefinition('cmobi_rabbitmq.rpc.test'));
    }

    private function declareServiceMock(ContainerBuilder $container)
    {
        $container->registerExtension(new CmobiRabbitmqExtension());
        $container->register('cmobi_rabbitmq.rpc.test')
            ->setPublic(false);
        $container->register('cmobi_rabbitmq.connection.default')
            ->setPublic(false);
        $container->register('cmobi_rabbitmq.logger')
            ->setPublic(false);
    }

    protected function process(ContainerBuilder $container)
    {
        $rpcServerPass = new RpcServerPass(
            'test',
            'cmobi_rabbitmq.connection.default',
            'cmobi_rabbitmq.message.handler',
            1,
            false,
            true,
            []
        );
        $rpcServerPass->process($container);
    }
}