<?php

namespace Cmobi\RabbitmqBundle\Transport\Subscriber;

class ExchangeType
{
    const FANOUT = 'fanout';
    const DIRECT = 'direct';
    const TOPIC = 'topic';
}
