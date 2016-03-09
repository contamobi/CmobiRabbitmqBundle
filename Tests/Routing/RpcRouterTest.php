<?php

namespace Cmobi\RabbitmqBundle\Tests\Routing;

use Cmobi\RabbitmqBundle\Routing\Method;
use Cmobi\RabbitmqBundle\Routing\MethodCollection;
use Cmobi\RabbitmqBundle\Routing\MethodRouter;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;

class RpcRouterTest extends BaseTestCase
{
    public function testRouterDump()
    {
        $methodCollection = new MethodCollection();
        $method = new Method(null, 'foo');
        $methodCollection->add('foo', $method);
        $sc = $this->getServiceContainer($methodCollection);
        $sc->setParameter('parameter.foo', 'foo');

        $router = new MethodRouter($sc, 'foo');
        $parameters = $router->match('foo');

        $this->assertEquals('foo', $parameters['_method']);
    }

    public function testRouterRpcService()
    {
        $methodCollection = new MethodCollection();
        $method = new Method(null, 'foo');
        $methodCollection->add('foo', $method);
        $sc = $this->getServiceContainer($methodCollection);
        $sc->setParameter('parameter.foo', 'foo');

        $router = new MethodRouter($sc, 'foo');

        $method = $router->getMethodCollection()->get('foo');

        $this->assertEquals(
            'foo',
            $method->getName()
        );
    }

    /**
     * @param MethodCollection $methods
     *
     * @return \Symfony\Component\DependencyInjection\Container
     */
    private function getServiceContainer(MethodCollection $methods)
    {
        $loader = $this->getMock('Symfony\Component\Config\Loader\LoaderInterface');

        $loader
            ->expects($this->any())
            ->method('load')
            ->will($this->returnValue($methods))
        ;

        $sc = $this->getMock('Symfony\\Component\\DependencyInjection\\Container', array('get'));

        $sc
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($loader))
        ;

        return $sc;
    }

}