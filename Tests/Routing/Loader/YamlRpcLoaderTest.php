<?php

namespace Cmobi\RabbitmqBundle\Tests\Routing\Loader;

use Cmobi\RabbitmqBundle\Routing\Loader\YamlRpcLoader;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;

class YamlRpcLoaderTest extends BaseTestCase
{

    public function testLoadRouteInstance()
    {
        $parser = $this->createParser();
        $path = __DIR__ . '/../../app/config';
        $loader = new YamlRpcLoader($this->getContainer(), $path, $parser);
        $methodCollection = $loader->load('rpc_routing.yml');
        $method = $methodCollection->get('cmobi_rabbitmq');

        $this->assertInstanceOf('Cmobi\RabbitmqBundle\Routing\Method', $method);
    }

    public function testLoadRouteName()
    {
        $parser = $this->createParser();
        $path = __DIR__ . '/../../app/config';
        $loader = new YamlRpcLoader($this->getContainer(), $path, $parser);
        $methodCollection = $loader->load('rpc_routing.yml');
        $method = $methodCollection->get('cmobi_rabbitmq');

        $this->assertSame('default', $method->getName());
    }

    private function createParser()
    {
        $bundles = array(
            'FooBundle' => array($this->getBundle('TestBundle\FooBundle', 'FooBundle')),
        );

        $kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
        $kernel
            ->expects($this->any())
            ->method('getBundle')
            ->will($this->returnCallback(function ($bundle) use ($bundles) {
                if (!isset($bundles[$bundle])) {
                    throw new \InvalidArgumentException(sprintf('Invalid bundle name "%s"', $bundle));
                }

                return $bundles[$bundle];
            }))
        ;
        $bundles = [
            'FooBundle' => $this->getBundle('Cmobi\RabbitmqBUndle\FooBundle', 'CmobiRabbitmqBundle'),
        ];
        $kernel
            ->expects($this->any())
            ->method('getBundles')
            ->will($this->returnValue($bundles))
        ;

        return new ControllerNameParser($kernel);
    }

    private function getBundle($namespace, $name)
    {
        $bundle = $this->getMock('Symfony\Component\HttpKernel\Bundle\BundleInterface');
        $bundle->expects($this->any())->method('getName')->will($this->returnValue($name));
        $bundle->expects($this->any())->method('getNamespace')->will($this->returnValue($namespace));

        return $bundle;
    }
}