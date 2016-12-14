<?php

namespace Cmobi\RabbitmqBundle\Transport\PubSub;

class ExchangeType
{
    const FANOUT = 'fanout';
    const DIRECT  = 'direct';
    const TOPIC = 'topic';
}