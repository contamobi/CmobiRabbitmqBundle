<?php

namespace Cmobi\RabbitmqBundle\Rpc;

use Cmobi\RabbitmqBundle\Routing\MethodRouter;
use Cmobi\RabbitmqBundle\Rpc\Exception\RpcGenericErrorException;
use Cmobi\RabbitmqBundle\Rpc\Exception\RpcParserErrorException;
use Cmobi\RabbitmqBundle\Rpc\Request\RpcRequestCollection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class RpcMessager
{
    private $router;
    private $encoder;

    public function __construct(
        MethodRouter $router,
        SerializerInterface $encoder = null
    )
    {
        $this->router = $router;

        if (is_null($encoder)) {
            $encoders = [new JsonEncoder()];
            $normalizers = [new ObjectNormalizer()];
            $this->encoder = new Serializer($normalizers, $encoders);
        }
    }

    /**
     * @param AMQPMessage $message
     * @param string $type
     * @return RpcRequestCollection
     * @throws RpcParserErrorException
     */
    public function parseAMQPMessage(AMQPMessage $message, $type = 'json')
    {
        $body = $message->body;
        $requestCollection = new RpcRequestCollection();

        try {
            /**
             * @var RpcRequestCollection $requests
             */
            $requests = $this->encoder->deserialize($body, RpcRequestCollection::class, $type);
        } catch (\Exception $e) {
            throw new RpcParserErrorException();
        }

        foreach ($requests->all() as $request) {
            $this->buildRequest($request);
            $requestCollection->add($request);
        }

        return $requestCollection;
    }

    private function buildRequest($request)
    {
        try {
            $this->router->setContext($request);

            if (!$request->attributes->has('_controller')) {
                $parameters = $this->router->match($request->getMethod());
                $request->attributes->add($parameters);
            }
        } catch (RpcGenericErrorException $e) {
            $request->attributes->add(['error' => $e]);
        }

        return $request;
    }

    /**
     * @return Serializer
     */
    public function getEncoder()
    {
        return $this->encoder;
    }

    /**
     * @param Serializer $encoder
     */
    public function setEncoder($encoder)
    {
        $this->encoder = $encoder;
    }
}