<?php

namespace Cmobi\RabbitmqBundle\Pool;

class Autoloader extends \Worker
{
    protected $loader;

    public function __construct($loader)
    {
        $this->loader = $loader;
    }

    /* include autoloader for Tasks */
    public function run()
    {
        require_once($this->loader);
    }

    /* override default inheritance behaviour for the new threaded context */
    public function start(int $options = PTHREADS_INHERIT_ALL)
    {
        return parent::start(PTHREADS_INHERIT_NONE);
    }
}


