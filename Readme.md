# Mq2phpBundle for real asynchronous messages

This bundle is a bridge between [SimpleBus](http://simplebus.github.io/MessageBus/) and [mq2php](https://github.com/Happyr/mq2php). 
It could be used together with the [SimpleBusAsynchronousBundle](http://simplebus.github.io/AsynchronousBundle/doc/getting_started.html)
to make the asynchronous messages independent of a cron job to consume the messages. Instead we utilize the 
power of PHP-FPM to schedule workload and resources. 

## Consuming messages from the queue

We do not want to run a cron command to consume messages from the queue because of two reasons. It takes a lot of 
computer resources to create a new thread and if we only do one task there will be a lot of overhead. The second reason
is that we want to be able to do resource scheduling. With a cronjob we say "*Consume this message at the next minute no
matter your current load*". Instead we would like to do something like: "*Consume this message as soon as possible*".

The solution to these problems is nothing new. It is actually exact the problems PHP-FPM is solving. We just need a way to 
pull messages from the message queue and give those to PHP-FPM. 

This is where [mq2php](https://github.com/Happyr/mq2php) comes in. It is a Java application that will run in the 
background. Java is preferred because it is build to run for ever. (Compared with PHP that should never be running for
 more than 30 seconds.)

## Installation

Fetch [mq2php.jar version 0.5.0](https://github.com/Happyr/mq2php/releases) or above and 
start the application with: 
```bash
java -Dexecutor=fastcgi -DmessageQueue=rabbitmq -DqueueNames=asynchronous_commands,asynchronous_events -jar mq2php.jar
```

You should consider using the init script when you using it on the production server. 

Install and enable this bundle

```bash
composer require happyr/mq2php-bundle
```

```php
class AppKernel extends Kernel
{
  public function registerBundles()
  {
    $bundles = array(
        // ...
        new Happyr\Mq2phpBundle\HappyrMq2phpBundle(),
        new SimpleBus\AsynchronousBundle\SimpleBusAsynchronousBundle(),
    }
  }
}
```

## Configuration

```yaml
// config.yml

old_sound_rabbit_mq:
  producers:
    asynchronous_commands:
      connection:       default
      exchange_options: { name: 'asynchronous_commands', type: direct }
      queue_options:    { name: 'asynchronous_commands' }

    asynchronous_events:
      connection:       default
      exchange_options: { name: 'asynchronous_events', type: direct }
      queue_options:    { name: 'asynchronous_events' }

simple_bus_rabbit_mq_bundle_bridge:
  commands:
    producer_service_id: old_sound_rabbit_mq.asynchronous_commands_producer
  events:
    producer_service_id: old_sound_rabbit_mq.asynchronous_events_producer
    
happyr_mq2php:
  enabled: true
  command_queue: 'asynchronous_commands' # The name of the RabbitMQ queue for commands
  event_queue: 'asynchronous_events' # The name of the RabbitMQ queue for events
  message_headers: 
    fastcgi_host: localhost
    fastcgi_port: 9000
    dispatch_path: "%kernel.root_dir%/dispatch-message.php"
```    

### HTTP executor

If you are not using fastcgi (eg PHP-FPM) you may use HTTP.

```
happyr_mq2php:
  message_headers: 
    http_url: https://example.com/dispatch-message.php
```   

### Shell executor

When debugging you may want to use the shell executor. This will require more CPU resources by mq2php since
starting a new process to for each message is heavy. 

```
happyr_mq2php:
  message_headers: 
    dispatch_path: "%kernel.root_dir%/dispatch-message.php"
```    