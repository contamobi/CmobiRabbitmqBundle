<?php

namespace Cmobi\RabbitmqBundle\Routing\Matcher;

use Cmobi\RabbitmqBundle\Rpc\Request\JsonRpcRequest;

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
     * @param JsonRpcRequest $context The context
     */
    public function setContext(JsonRpcRequest $context);

    /**
     * Gets the request context.
     *
     * @return JsonRpcRequest The context
     */
    public function getContext();
}