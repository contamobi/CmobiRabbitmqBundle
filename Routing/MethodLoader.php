<?php

namespace Cmobi\RabbitmqBundle\Routing;

use Cmobi\RabbitmqBundle\Routing\MethodCollection;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class MethodLoader extends Loader
{
    use ContainerAwareTrait;

    public function load($resource, $type = null)
    {
        $collection = new MethodCollection();
        $root = $this->getContainer()->getParameter('kernel.root_dir');
        $resource = $root . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'rpc_routing.yml';
        $type = 'yaml';
        $importedRouters = $this->import($resource, $type);
        $collection->addCollection($importedRouters);

        return $collection;
    }

    public function supports($resource, $type = null)
    {
        return 'rpc' === $type;
    }

    public function getContainer()
    {
        return $this->container;
    }
}