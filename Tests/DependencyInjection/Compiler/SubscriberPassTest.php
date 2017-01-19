<?php

namespace Cmobi\RabbitmqBundle\Tests\DependencyInjection\Compiler;

use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\DependencyInjection\CmobiRabbitmqExtension;
use Cmobi\RabbitmqBundle\DependencyInjection\Compiler\SubscriberPass;
use Cmobi\RabbitmqBundle\Queue\BaseQueueService;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use Cmobi\RabbitmqBundle\Transport\Subscriber\ExchangeType;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SubscriberPassTest extends BaseTestCase
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

        $this->assertTrue($container->hasDefinition('cmobi_rabbitmq.pub_sub.test'));
    }

    private function declareServiceMock(ContainerBuilder $container)
    {
        $container->registerExtension(new CmobiRabbitmqExtension());
        $container->register('cmobi_rabbitmq.pub_sub.test')
            ->setPublic(false);
        $container->register('cmobi_rabbitmq.connection.default')
            ->setPublic(false);
        $container->register('cmobi_rabbitmq.logger')
            ->setPublic(false);
    }

    protected function process(ContainerBuilder $container)
    {
        $subscriberPass = new SubscriberPass(
            'exchange_test',
            ExchangeType::FANOUT,
            'test',
            'cmobi_rabbitmq.connection.default',
            'cmobi_rabbitmq.message.handler',
            1,
            []
        );
        $subscriberPass->process($container);
    }
}