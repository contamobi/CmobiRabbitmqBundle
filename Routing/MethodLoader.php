<?php

namespace Cmobi\CmobiRabbitmqBundle\Routing;

use Cmobi\RabbitmqBundle\Routing\MethodCollection;
use Symfony\Component\Config\Loader\Loader;

class MethodLoader extends Loader
{

    public function load($resource, $type = null)
    {
        $collection = new MethodCollection();
        $resource = 'app/config/rpc_routing.yml';
        $type = 'yaml';
        $importedRouters = $this->import($resource, $type);
        $collection->addCollection($importedRouters);

        return $collection;
    }

    public function supports($resource, $type = null)
    {
        return 'rpc' === $type;
    }
}