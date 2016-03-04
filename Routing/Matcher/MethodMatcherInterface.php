<?php

namespace Cmobi\RabbitmqBundle\Routing\Matcher;

interface MethodMatcherInterface
{
    /**
     * Tries to match METHOD with a set of routes.
     *
     * @param string $path
     * @return array
     */
    public function match($path);
}