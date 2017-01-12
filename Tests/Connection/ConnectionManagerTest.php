<?php

namespace Cmobi\RabbitmqBundle\Tests\Connection;

use Cmobi\RabbitmqBundle\Connection\ConnectionManager;
use Cmobi\RabbitmqBundle\Tests\BaseTestCase;

class ConnectionManagerTest extends BaseTestCase
{
    public function testGetDefault()
    {
        $manager = new ConnectionManager([]);
        $this->assertEquals('default', $manager->getDefault());
    }

    public function testGetDefaultChanged()
    {
        $manager = new ConnectionManager([]);
        $manager->setDefault('changed');
        $this->assertEquals('changed', $manager->getDefault());
    }
}