<?php

namespace Cmobi\RabbitmqBundle\Routing;

use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\ResourceInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class MethodCollectionBuilder
{
    private $methods = [];

    private $loader;
    private $defaults = [];
    private $resources = [];

    /**
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader = null)
    {
        $this->loader = $loader;
    }

    public function import($resource, $type = null)
    {
        $collection = $this->load($resource, $type);

        // create a builder from the MethodCollection
        $builder = $this->createBuilder();
        foreach ($collection->all() as $name => $route) {
            $builder->addMethod($route, $name);
        }

        foreach ($collection->getResources() as $resource) {
            $builder->addResource($resource);
        }
        $this->mount($builder);

        return $builder;
    }

    public function add($path, $controller, $name)
    {
        $method = new Method(null, $path);
        $method->setDefault('_controller', $controller);
        $this->addMethod($method, $name);

        return $method;
    }

    public function createBuilder()
    {
        return new self($this->loader);
    }

    public function mount(MethodCollectionBuilder $builder)
    {
        $this->methods[] = $builder;
    }

    public function addMethod(Method $route, $name)
    {
        if (null === $name) {
            throw new  InvalidArgumentException('name can\'t be null');
        }

        $this->methods[$name] = $route;

        return $this;
    }

    public function setDefault($key, $value)
    {
        $this->defaults[$key] = $value;

        return $this;
    }

    private function addResource(ResourceInterface $resource)
    {
        $this->resources[] = $resource;

        return $this;
    }

    public function build()
    {
        $methodCollection = new MethodCollection();

        foreach ($this->methods as $name => $method) {
            if ($method instanceof Method) {
                $method->setDefaults(array_merge($this->defaults, $method->getDefaults()));

                $methodCollection->add($name, $method);
            } else {
                /* @var self $route */
                $subCollection = $route->build();

                $methodCollection->addCollection($subCollection);
            }

            foreach ($this->resources as $resource) {
                $methodCollection->addResource($resource);
            }
        }

        return $methodCollection;
    }

    /**
     * @param $resource
     * @param null $type
     * @return mixed
     * @throws FileLoaderLoadException
     */
    private function load($resource, $type = null)
    {
        if (null === $this->loader) {
            throw new \BadMethodCallException('Cannot import other routing resources: you must pass a LoaderInterface when constructing RouteCollectionBuilder.');
        }

        if ($this->loader->supports($resource, $type)) {
            return $this->loader->load($resource, $type);
        }

        if (null === $resolver = $this->loader->getResolver()) {
            throw new FileLoaderLoadException($resource);
        }

        if (false === $loader = $resolver->resolve($resource, $type)) {
            throw new FileLoaderLoadException($resource);
        }

        return $loader->load($resource, $type);
    }
}
