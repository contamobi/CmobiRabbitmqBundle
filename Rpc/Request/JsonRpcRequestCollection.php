<?php

namespace Cmobi\RabbitmqBundle\Rpc\Request;


class JsonRpcRequestCollection implements RpcRequestCollectionInterface, \IteratorAggregate, \Countable
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

    public function add($id, RpcRequestInterface $request)
    {
        unset($this->requests[$id]);

        $this->requests[$id] = $request;
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

    public function remove($id)
    {
        unset($this->requests[$id]);
    }

    public function addCollection(JsonRpcRequestCollection $collection)
    {
        foreach ($collection->all() as $id => $request) {
            unset($this->requests[$id]);
            $this->requests[$id] = $request;
        }
    }

    public function __toString()
    {
        $requests = [];

        foreach ($this->requests as $request) {
            $request = json_decode($request, true);
            $requests[] = $request;
        }

        return json_encode($requests);
    }
}