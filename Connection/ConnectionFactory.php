<?php

namespace Cmobi\RabbitmqBundle\Connection;

use Cmobi\RabbitmqBundle\Connection\Exception\InvalidAMQPConnectionClassException;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Class based on AMQPConnectionFactory from OldSoundRabbitmqBundle
 * Class ConnectionFactory.
 */
class ConnectionFactory
{
    /** @var \ReflectionClass */
    private $class;

    /** @var array */
    private $parameters = [
        'host' => 'localhost',
        'port' => 5672,
        'user' => 'guest',
        'password' => 'guest',
        'vhost' => '/',
        'connection_timeout' => 3,
        'read_write_timeout' => 3,
        'ssl_context' => null,
        'keepalive' => false,
        'heartbeat' => 0,
    ];

    /**
     * @param $class string FQCN of AMQPConnection class to instantiate.
     * @param array $parameters
     *
     * @throws InvalidAMQPConnectionClassException
     */
    public function __construct($class, array $parameters)
    {
        if (!is_a($class, AMQPStreamConnection::class, true)) {
            throw new InvalidAMQPConnectionClassException('$class not instance of AMQPStreamConnection');
        }
        $this->class = $class;
        $this->parameters = array_merge($this->parameters, $parameters);

        if (is_array($this->parameters['ssl_context'])) {
            $this->parameters['ssl_context'] = null;

            if (!empty($this->parameters['ssl_context'])) {
                stream_context_create(['ssl' => $this->parameters['ssl_context']]);
            }
        }
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return \ReflectionClass|string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return CmobiAMQPConnectionInterface
     */
    public function createConnection()
    {
        return new $this->class(
            $this->parameters['host'],
            $this->parameters['port'],
            $this->parameters['user'],
            $this->parameters['password'],
            $this->parameters['vhost'],
            false,      // insist
            'AMQPLAIN', // login_method
            null,       // login_response
            'en_US',    // locale
            $this->parameters['connection_timeout'],
            $this->parameters['read_write_timeout'],
            $this->parameters['ssl_context'],
            $this->parameters['keepalive'],
            $this->parameters['heartbeat']
        );
    }
}
