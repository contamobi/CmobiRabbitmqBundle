<?php

namespace Cmobi\RabbitmqBundle\Routing\Matcher;

use Cmobi\RabbitmqBundle\Rpc\Request\RpcRequest;

interface MethodMatcherInterface
{
    /**
     * Tries to match METHOD with a set of routes.
     *
     * @param string $path
     * @return array
     */
    public function match($path);

    /**
     * Sets the request context.
     *
     * @param RpcRequest $context The context
     */
    public function setContext(RpcRequest $context);

    /**
     * Gets the request context.
     *
     * @return RpcRequest The context
     */
    public function getContext();
}