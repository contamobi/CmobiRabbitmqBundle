<?php

namespace Cmobi\RabbitmqBundle;

use Cmobi\RabbitmqBundle\DependencyInjection\CmobiRabbitmqExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CmobiRabbitmqBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new CmobiRabbitmqExtension();
    }
}
