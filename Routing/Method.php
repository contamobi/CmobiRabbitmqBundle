<?php

namespace Cmobi\RabbitmqBundle\Routing;

class Method implements \Serializable
{

    private $id;
    private $name;
    private $params;
    private $defaults = [];
    private  $options = [];
    private $compiled;

    public function __construct($id, $name, array $defaults = [], array $options = [], array $params = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->setDefaults($defaults);
        $this->params = $params;
    }


    public function serialize()
    {
        return serialize([
           'id' => $this->id,
            'name' => $this->name,
            'params' => $this->params,
            'defaults' => $this->defaults,
            'compiled' => $this->compiled
        ]);
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->setId($data['id']);
        $this->setName($data['name']);
        $this->setParams($data['params']);
        $this->setDefaults($data['defaults']);

        if (isset($data['compiled'])) {
            $this->compiled =$data['compiled'];
        }
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

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions(array $options)
    {
        $this->options = array(
            'compiler_class' => 'Cmobi\\RabbitmqBundle\\Routing\\MethodCompiler',
        );

        return $this->addOptions($options);
    }

    public function addOptions(array $options)
    {
        foreach ($options as $name => $option) {
            $this->options[$name] = $option;
        }
        $this->compiled = null;

        return $this;
    }

    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
        $this->compiled = null;

        return $this;
    }

    public function getOption($name)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        return null;
    }

    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    public function setDefaults(array $defaults)
    {
        $this->defaults = array();

        return $this->addDefaults($defaults);
    }

    public function addDefaults(array $defaults)
    {
        foreach ($defaults as $name => $default) {
            $this->defaults[$name] = $default;
        }
        $this->compiled = null;

        return $this;
    }

    public function getDefault($name)
    {
        if (isset($this->defaults[$name])) {
            return $this->defaults[$name];
        }

        return null;
    }

    public function hasDefault($name)
    {
        return array_key_exists($name, $this->defaults);
    }

    public function setDefault($name, $default)
    {
        $this->defaults[$name] = $default;
        $this->compiled = null;

        return $this;
    }

    public function compile()
    {
        if (null !== $this->compiled) {
            return $this->compiled;
        }

        $class = $this->getOption('compiler_class');

        return $this->compiled = $class::compile($this);
    }
}