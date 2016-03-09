<?php

namespace Cmobi\RabbitmqBundle\Routing\Matcher\Dumper;

use Cmobi\RabbitmqBundle\Routing\MethodCollection;

abstract class MatcherDumper
{

    private $methods;

    public function __construct(MethodCollection $method)
    {
        $this->methods = $method;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethods()
    {
        return $this->methods;
    }
}
