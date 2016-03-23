<?php

namespace Cmobi\RabbitmqBundle\Rpc\Request;


class RpcRequestCollection implements RpcRequestCollectionInterface, \IteratorAggregate, \Countable
{
    private $priority;
    public $requests = [];

    public function __construct($priority = RpcRequestCollectionInterface::PRIORITY_LOW)
    {
        $this->priority = $this->changePriority($priority);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->requests);
    }

    public function count()
    {
        return count($this->requests);
    }

    public function add(RpcRequestInterface $request)
    {
        if (!is_null($request)) {
            unset($this->requests[$request->getId()]);
            $this->requests[$request->getId()] = $request;
        } else {
            $key = array_search($request, $this->requests, true);
            unset($this->requests[$key]);
            $this->requests[] = $request;
        }
    }

    public function all()
    {
        return $this->requests;
    }

    public function get($id)
    {
        if (isset($this->requests[$id])) {
            return $this->requests[$id];
        }

        return null;
    }

    /**
     * @param RpcRequest $request
     * @return string|int|null
     */
    public function getRequestIndex(RpcRequest $request)
    {
        return array_search($request, $this->requests);
    }

    /**
     * @param $id
     */
    public function remove($id)
    {
        unset($this->requests[$id]);
    }

    /**
     * @param RpcRequestCollection $collection
     */
    public function addCollection(RpcRequestCollection $collection)
    {
        foreach ($collection->all() as $id => $request) {
            unset($this->requests[$id]);
            $this->requests[$id] = $request;
        }
    }

    /**
     * @param $priorityNumber
     * @return string
     */
    public function changePriority($priorityNumber)
    {
        if (!decoct(octdec($priorityNumber) == $priorityNumber)) {
            $priorityNumber = decoct($priorityNumber);
        }
        $this->priority = $priorityNumber;

        return $this->priority;
    }

    /**
     * Return octect priority
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }
}