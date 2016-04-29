<?php

namespace Cmobi\RabbitmqBundle\Rpc\Response;


class RpcResponseCollection implements RpcResponseCollectionInterface, \IteratorAggregate, \Countable
{
    public $responses = [];

    public function getIterator()
    {
        return new \ArrayIterator($this->responses);
    }

    public function count()
    {
        return count($this->responses);
    }

    public function add(RpcResponseInterface $response)
    {
        if (!is_null($response->getId())) {
            unset($this->responses[$response->getId()]);
            $this->responses[$response->getId()] = $response;
        } else {
            $key = array_search($response, $this->responses, true);
            unset($this->responses[$key]);

            $this->responses[] = $response;
        }
    }

    public function all()
    {
        return $this->responses;
    }


    public function get($id)
    {
        if (isset($this->responses[$id])) {
            return $this->responses[$id];
        }

        return null;
    }

    public function remove($id)
    {
        unset($this->responses[$id]);
    }

    public function clear()
    {
        $this->responses = [];
    }

    public function addCollection(RpcResponseCollection $collection)
    {
        foreach ($collection->all() as $id => $response) {
            unset($this->responses[$id]);
            $this->responses[$id] = $response;
        }
    }
}