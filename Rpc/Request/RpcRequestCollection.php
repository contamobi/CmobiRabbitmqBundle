<?php

namespace Cmobi\RabbitmqBundle\Rpc\Request;


class RpcRequestCollection implements RpcRequestCollectionInterface, \IteratorAggregate, \Countable
{
    public $requests = [];

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
        $key = array_search($request, $this->requests, true);

        if ($key !== false) {
            unset($this->requests[$key]);
        }
        $this->requests[] = $request;
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

    public function getRequestIndex(RpcRequest $request)
    {
        return array_search($request, $this->requests);
    }

    public function remove($id)
    {
        unset($this->requests[$id]);
    }

    public function addCollection(RpcRequestCollection $collection)
    {
        foreach ($collection->all() as $id => $request) {
            unset($this->requests[$id]);
            $this->requests[$id] = $request;
        }
    }
}