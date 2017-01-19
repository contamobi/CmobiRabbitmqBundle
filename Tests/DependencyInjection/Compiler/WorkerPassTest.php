<?php

namespace Cmobi\RabbitmqBundle\Tests\DependencyInjection\Compiler;

use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\DependencyInjection\CmobiRabbitmqExtension;
use Cmobi\RabbitmqBundle\DependencyInjection\Compiler\WorkerPass;
use Cmobi\RabbitmqBundle\Queue\BaseQueueService;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class WorkerPassTest extends BaseTestCase
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

        $this->assertTrue($container->hasDefinition('cmobi_rabbitmq.worker.test'));
    }

    private function declareServiceMock(ContainerBuilder $container)
    {
        $container->registerExtension(new CmobiRabbitmqExtension());
        $container->register('cmobi_rabbitmq.worker.test')
            ->setPublic(false);
        $container->register('cmobi_rabbitmq.connection.default')
            ->setPublic(false);
        $container->register('cmobi_rabbitmq.logger')
            ->setPublic(false);
    }

    protected function process(ContainerBuilder $container)
    {
        $workerPass = new WorkerPass(
            'test',
            'cmobi_rabbitmq.connection.default',
            'cmobi_rabbitmq.message.handler',
            1,
            []
        );
        $workerPass->process($container);
    }
}