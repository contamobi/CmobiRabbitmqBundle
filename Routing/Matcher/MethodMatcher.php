<?php

namespace Cmobi\RabbitmqBundle\Routing\Matcher;

use Cmobi\RabbitmqBundle\Routing\MethodCollection;

class MethodMatcher implements MethodMatcherInterface
{
    /**
     * Tries to match METHOD with a set of routes.
     *
     * @param string $path
     * @return array
     */
    public function match($path)
    {
        $methodCollection = new MethodCollection();
    }

    public function matchCollection($path, MethodCollection $methods)
    {

    }
}
