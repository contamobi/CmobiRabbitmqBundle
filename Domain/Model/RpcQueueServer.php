<?php
namespace Cmobi\RabbitmqBundle\Domain\Model;

use Cmobi\RabbitmqBundle\Domain\TimestampableTrait;

class RpcQueueServer
{
    use TimestampableTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $process;

    /**
     * @var string
     */
    private $queue;

    /**
     * @var boolean
     */
    private $busy;

    /**
     * @param int $id
     * @return RpcQueueServer
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $queue
     * @return RpcQueueServer
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;
        return $this;
    }

    /**
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @return bool
     */
    public function isBusy()
    {
        return $this->busy;
    }

    /**
     * @param bool $busy
     * @return $this
     */
    public function setBusy($busy)
    {
        $this->busy = $busy;
        return $this;
    }

    /**
     * @return string
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @param string $process
     * @return $this
     */
    public function setProcess($process)
    {
        $this->process = $process;
        return $this;
    }


}