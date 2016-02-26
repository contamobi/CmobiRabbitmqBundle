## CmobiRabbitmqBundle ##

[![Build Status](https://travis-ci.org/contamobi/CmobiRabbitmqBundle.svg?branch=master)](http://travis-ci.org/contamobi/CmobiRabbitmqBundle)
[![Coverage Status](https://coveralls.io/repos/github/contamobi/CmobiRabbitmqBundle/badge.svg?branch=master)](https://coveralls.io/github/contamobi/CmobiRabbitmqBundle?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/contamobi/CmobiRabbitmqBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/contamobi/CmobiRabbitmqBundle/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/56d0b268157a690037bbb6e8/badge.svg?style=flat)](https://www.versioneye.com/user/projects/56d0b268157a690037bbb6e8)

[![Latest Stable Version](https://poser.pugx.org/cmobi/rabbitmq-bundle/v/stable)](https://packagist.org/packages/cmobi/rabbitmq-bundle)
[![Latest Unstable Version](https://poser.pugx.org/cmobi/rabbitmq-bundle/v/unstable)](https://packagist.org/packages/cmobi/rabbitmq-bundle)
[![Total Downloads](https://poser.pugx.org/cmobi/rabbitmq-bundle/downloads)](https://packagist.org/packages/cmobi/rabbitmq-bundle)
[![License](https://poser.pugx.org/cmobi/rabbitmq-bundle/license)](https://packagist.org/packages/cmobi/rabbitmq-bundle)

The bundle provides a [RabbitMq](http://rabbitmq.com/) integration for your Symfony2 Project. Based on [php-amqplib](https://github.com/php-amqplib/php-amqplib).

## Instalation ##

```
$ composer require cmobi/rabbitmq-bundle --no-update
```

Register the bundle:

``` php
// app/AppKernel.php

public function registerBundles()
{
    return array(
        new \Cmobi\RabbitmqBundle\CmobiRabbitmqBundle(),
        // ...
    );
}
```

Install the bundle:

```
$ composer update cmobi/rabbitmq-bundle
```

## Usage: ##

Add `cmobi_rabbitmq` section in your configuration file:

```yaml
cmobi_rabbitmq:
    connections:
        default:
            host: 172.17.0.1
            port: 5672
            user:     'guest'
            password: 'guest'
            vhost:    '/'
            lazy:     false
            connection_timeout: 3
            read_write_timeout: 3
            # requires php-amqplib v2.4.1+ and PHP5.4+
            keepalive: false
            # requires php-amqplib v2.4.1+
            heartbeat: 0
```

Register rpc servers:

```yaml
cmobi_rabbitmq:
   //...
    rpc_servers:
        primary_server:
            queue: { name: 'primary_queue' }
            class: AppBundle\Rpc\PrimaryService
            arguments: []
        second_server:
            queue: { name: 'second_queue' }
            class: AppBundle\Rpc\SecondService
            arguments: []
```

The `class` file is automatic declared as service on project load, and `arguments` is attributed to service. `queue` parameters inform how qeue should be created with exemple queue `name`.


