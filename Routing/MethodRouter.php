<?php

namespace Cmobi\RabbitmqBundle\Routing;

use Cmobi\RabbitmqBundle\Routing\Matcher\MethodMatcherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Config\ConfigCacheInterface;
use Symfony\Component\Config\ConfigCacheFactoryInterface;
use Symfony\Component\Config\ConfigCacheFactory;
use Symfony\Component\Routing\RequestContext;

class MethodRouter
{
    protected $matcher;
    protected $context;
    protected $loader;
    protected $collection;
    protected $resource;
    protected $options = [];
    private $configCacheFactory;

    public function __construct(ContainerInterface $loader, $resource, array $options = [])
    {
        $this->loader = $loader;
        $this->resource = $resource['resource'];
        $this->context = new Method(null, '');
        $this->setOptions($options);
    }

    public function setOptions(array $options)
    {
        $this->options = [
            'cache_dir' => null,
            'debug' => false,
            'matcher_class' => 'Cmobi\\RabbitmqBundle\\Routing\\Matcher\\MethodMatcher',
            'matcher_dumper_class' => 'Cmobi\\RabbitmqBundle\\Routing\\Matcher\\Dumper\\PhpMatcherDumper',
            'matcher_cache_class' => 'ProjectMethodMatcher',
            'resource_type' => null,
            'strict_requirements' => true,
        ];
        $invalid = [];

        foreach ($options as $key => $value) {

            if (array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            } else {
                $invalid[] = $key;
            }
        }

        if ($invalid) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the following options: "%s".', implode('", "', $invalid)));
        }
    }

    public function setOption($key, $value)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the "%s" option.', $key));
        }

        $this->options[$key] = $value;
    }

    public function getOption($key)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the "%s" option.', $key));
        }

        return $this->options[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodCollection()
    {
        if (null === $this->collection) {
            $this->collection = $this->loader->get('routing.loader')->load($this->resource, $this->options['resource_type']);
        }

        return $this->collection;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;

        if (null !== $this->matcher) {
            $this->getMatcher()->setContext($context);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    public function setConfigCacheFactory(ConfigCacheFactoryInterface $configCacheFactory)
    {
        $this->configCacheFactory = $configCacheFactory;
    }

    public function warmUp($cacheDir)
    {
        $currentDir = $this->getOption('cache_dir');

        // force cache generation
        $this->setOption('cache_dir', $cacheDir);
        $this->getMatcher();

        $this->setOption('cache_dir', $currentDir);
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        return $this->getMatcher()->match($pathinfo);
    }

    /**
     * @return MethodMatcherInterface A MethodMatcherInterface instance
     */
    public function getMatcher()
    {
        if (null !== $this->matcher) {
            return $this->matcher;
        }

        if (null === $this->options['cache_dir'] || null === $this->options['matcher_cache_class']) {
            $this->matcher = new $this->options['matcher_class']($this->getMethodCollection(), $this->context);

            return $this->matcher;
        }

        $class = $this->options['matcher_cache_class'];
        $that = $this;

        $cache = $this->getConfigCacheFactory()->cache($this->options['cache_dir'].'/'.$class.'.php',
            function (ConfigCacheInterface $cache) use ($that, $class) {
                $dumper = $that->getMatcherDumperInstance();

                $options = array(
                    'class' => $class
                );

                $cache->write($dumper->dump($options), $that->getMethodCollection()->getResources());
            }
        );

        require_once $cache->getPath();

        return $this->matcher = new $class($this->context);
    }

    public function getGeneratorDumperInstance()
    {
        return new $this->options['generator_dumper_class']($this->getMethodCollection());
    }

    public function getMatcherDumperInstance()
    {
        return new $this->options['matcher_dumper_class']($this->getMethodCollection());
    }

    private function getConfigCacheFactory()
    {
        if (null === $this->configCacheFactory) {
            $this->configCacheFactory = new ConfigCacheFactory($this->options['debug']);
        }

        return $this->configCacheFactory;
    }
}
