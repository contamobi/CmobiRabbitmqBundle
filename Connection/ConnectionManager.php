<?php

namespace Cmobi\RabbitmqBundle\Connection;

use Cmobi\RabbitmqBundle\Connection\Exception\NotFoundAMQPConnectionFactoryException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ConnectionManager
{
    use ContainerAwareTrait;

    private $factories;
    private $default;

    public function __construct(array $factories)
    {
        $this->default = 'default';
        $this->factories = $factories;
    }

    public function getConnection($name = null)
    {
        $factory = $this->getContainer()->get($this->factories[$this->default]);

        if (!is_null($name) && array_key_exists($name, $this->factories)) {
            $factory = $this->getContainer()->get(
                $this->factories[$name]
            );
        }

        if (!$factory instanceof ConnectionFactory) {
            throw new NotFoundAMQPConnectionFactoryException(sprintf('%s: connection not found.', $name));
        }

        return $factory->createConnection();
    }

    /**
     * @param $connName
     */
    public function setDefault($connName)
    {
        $this->default = $connName;
    }

    /**
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
