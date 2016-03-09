<?php

namespace Cmobi\RabbitmqBundle\Routing;

use IteratorAggregate;
use Symfony\Component\Config\Resource\ResourceInterface;

class MethodCollection implements IteratorAggregate, \Countable
{
    private $methods = [];
    private  $resources = [];

    public function getIterator()
    {
        return new \ArrayIterator($this->methods);
    }

    public function count()
    {
        return count($this->methods);
    }

    public function add($name, Method $method)
    {
        unset($this->methods[$name]);

        $this->methods[$name] = $method;
    }

    public function all()
    {
        return $this->methods;
    }


    public function get($name)
    {
        if (isset($this->methods[$name])) {
            return $this->methods[$name];
        }

        return null;
    }

    /**
     * Removes a route or an array of routes by name from the collection.
     *
     * @param string|array $name The route name or an array of route names
     */
    public function remove($name)
    {
        foreach ((array) $name as $n) {
            unset($this->methods[$n]);
        }
    }

    public function addCollection(MethodCollection $collection)
    {
        // we need to remove all routes with the same names first because just replacing them
        // would not place the new route at the end of the merged array
        foreach ($collection->all() as $name => $route) {
            unset($this->methods[$name]);
            $this->methods[$name] = $route;
        }

        $this->resources = array_merge($this->resources, $collection->getResources());
    }

    public function getResources()
    {
        return array_unique($this->resources);
    }

    public function addResource(ResourceInterface $resource)
    {
        $this->resources[] = $resource;
    }
}