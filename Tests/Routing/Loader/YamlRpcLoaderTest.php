<?php

namespace Cmobi\RabbitmqBundle\Tests\Routing\Loader;

use Cmobi\RabbitmqBundle\Routing\Loader\YamlRpcLoader;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;
use Symfony\Component\Config\FileLocator;

class YamlRpcLoaderTest extends BaseTestCase
{

    public function testLoadRouteInstance()
    {
        $loader = new YamlRpcLoader(new FileLocator(array(__DIR__.'/../../Fixtures')));
        $methodCollection = $loader->load('validmethod.yml');
        $method = $methodCollection->get('cmobi_default');

        $this->assertInstanceOf('Cmobi\RabbitmqBundle\Routing\Method', $method);

    }

    public function testLoadRouteName()
    {
        $loader = new YamlRpcLoader(new FileLocator(array(__DIR__.'/../../Fixtures')));
        $methodCollection = $loader->load('validmethod.yml');
        $method = $methodCollection->get('cmobi_default');

        $this->assertSame('default', $method->getName());
    }
}