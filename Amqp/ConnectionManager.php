<?php

namespace Cmobi\RabbitmqBundle\Amqp;

use Cmobi\RabbitmqBundle\ConnectionManagerInterface;
use Cmobi\RabbitmqBundle\Amqp\Exception\NotFoundAMQPConnectionFactoryException;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ConnectionManager implements ConnectionManagerInterface
{
    use ContainerAwareTrait;

    private $connections;

    public function __construct(array $factories)
    {
        $this->connections = $factories;
    }

    /**
     * @param null $name
     * @return AMQPStreamConnection
     * @throws \Exception
     */
    public function getConnection($name = null)
    {
        $factory = $this->getContainer()->get($this->connections['default']);

        if (!is_null($name) && array_key_exists($name, $this->connections)) {
            $factory = $this->getContainer()->get(
                $this->connections[$name]
            );
        }

        if (!$factory instanceof ConnectionFactoryInterface) {
            throw new NotFoundAMQPConnectionFactoryException(sprintf('%s: connection not found.', $name));
        }

        return $factory->createConnection();
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}