<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\Rpc\Exception\InvalidBodyAMQPMessageException;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

class Handler
{
    use ContainerAwareTrait;

    private $resolver;

    public function __construct()
    {
        $this->resolver = new ControllerResolver();
    }

    public function handle(AMQPMessage $message)
    {

        //$controller = $this->getResolver()->getController($message);
        //$arguments = $this->getResolver()->getArguments($message, $controller);
        $controller = '';
        $arguments = [];
        $response = call_user_func_array($controller, $arguments);

        if (!is_string($response) || is_null($response)) {
            throw new InvalidBodyAMQPMessageException('Invalid Body: Content should be string and not null.');
        }
        $amqpResponse = new AMQPMessage(
            $response,
            ['correlation_id' => $message->get('correlation_id')]
        );

        return $amqpResponse;
    }

    /**
     * @return ControllerResolver
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}