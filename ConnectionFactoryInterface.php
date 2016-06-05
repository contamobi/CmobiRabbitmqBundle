<?php

namespace Cmobi\RabbitmqBundle;

interface ConnectionFactoryInterface
{
    public function createConnection();
}