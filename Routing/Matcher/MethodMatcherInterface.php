<?php

namespace Cmobi\RabbitmqBundle\Routing\Matcher;

use Symfony\Component\Routing\RequestContextAwareInterface;

interface MethodMatcherInterface extends RequestContextAwareInterface
{
    /**
     * Tries to match METHOD with a set of routes.
     *
     * @param string $path
     * @return array
     */
    public function match($path);
}