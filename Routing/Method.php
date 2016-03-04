<?php

namespace Cmobi\RabbitmqBundle\Routing;

class Method implements \Serializable
{

    private $id;
    private $name;
    private $params;

    public function __construct($id, $name, array $params = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->params = $params;
    }


    public function serialize()
    {
        return serialize([
           'id' => $this->getId(),
            'name' => $this->getName(),
            'params' => $this->getParams()
        ]);
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->setId($data['id']);
        $this->setName($data['name']);
        $this->setParams($data['params']);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }
}