<?php
namespace Cmobi\RabbitmqBundle\Domain\Service;


use Application\Infrastructure\Repository\RpcQueueServerRepository;
use Cmobi\RabbitmqBundle\Domain\Model\RpcQueueServer;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HealthCheckService
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RpcQueueServerRepository
     */
    private $rpcQueueServerRepository;

    /**
     * HealthCheckService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
        $em = $this->getContainer()->get('doctrine')->getManager('cmobi_healthcheck_manager');
        $this->setRpcQueueServerRepository($em->getRepository('RabbitmqBundle:RpcQueueServer'));
    }

    /**
     * @param RpcQueueServer $rpcQueueServer
     */
    public function startRpcQueueServer(RpcQueueServer $rpcQueueServer)
    {
        $repo = $this->getRpcQueueServerRepository();
        $rpcQueueServer = $repo->getServer($rpcQueueServer);
        $rpcQueueServer->setBusy(true);
        $repo->update();
    }

    /**
     * @param RpcQueueServer $rpcQueueServer
     */
    public function releaseRpcQueueServer(RpcQueueServer $rpcQueueServer)
    {
        $repo = $this->getRpcQueueServerRepository();
        $rpcQueueServer = $repo->getServer($rpcQueueServer);
        $rpcQueueServer->setBusy(false);
        $repo->update();
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container
     * @return $this
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @return RpcQueueServerRepository
     */
    public function getRpcQueueServerRepository()
    {
        return $this->rpcQueueServerRepository;
    }

    /**
     * @param RpcQueueServerRepository $rpcQueueServerRepository
     * @return $this
     */
    public function setRpcQueueServerRepository($rpcQueueServerRepository)
    {
        $this->rpcQueueServerRepository = $rpcQueueServerRepository;
        return $this;
    }
}