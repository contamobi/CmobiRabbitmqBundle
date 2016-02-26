<?php

namespace Cmobi\RabbitmqBundle\Tests;

use Symfony\Component\DependencyInjection\Container;

class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    private $container;

    protected function setUp()
    {
        $kernel = new \AppKernel('test', true);
        $kernel->boot();

        $this->container = $kernel->getContainer();
    }

    /**
     * @return Container
     */
    protected function getContainer()
    {
        return $this->container;
    }
}