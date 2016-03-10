<?php

namespace Cmobi\RabbitmqBundle\Rpc\Response;


class JsonRpcResponseCollection implements RpcResponseCollectionInterface, \IteratorAggregate, \Countable
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
        $key = array_search($response, $this->responses, true);

        if ($key !== false) {
            unserialize($this->responses[$key]);
        }
        unset($this->responses[$key]);

        $this->responses[] = $response;
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

    public function addCollection(JsonRpcResponseCollection $collection)
    {
        foreach ($collection->all() as $id => $response) {
            unset($this->responses[$id]);
            $this->responses[$id] = $response;
        }
    }

    public function __toString()
    {
        if (count($this->responses) == 1) {
            return (string)$this->responses[0];
        }
        $response = [];

        foreach ($this->responses as $response) {
            $response = json_decode($response, true);
            $response[] = $response;
        }

        return json_encode($response);
    }
}