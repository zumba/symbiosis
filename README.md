Symbiosis is a drop-in event driven plugin architecture.

The goal is to allow anyone to create a plugin structure for their existing code base using an event oriented system.
The secondary benefit of using Symbiosis is that the event structure can be used apart from the plugin structure.

__Current Version__: v1.1.5

[![Build Status](https://secure.travis-ci.org/zumba/symbiosis.png)](http://travis-ci.org/zumba/symbiosis)

## Requirements

PHP 5.3+

## Setup

1. Add as a composer dependency or install directly from composer.
1. That's it!

## Testing

1. Run `composer install --dev`.
2. Run `phpunit`.

## Example Plugin

### Plugin

```php
<?php

namespace \YourApp\Plugin;

use \Zumba\Symbiosis\Framework\Plugin,
    \Zumba\Symbiosis\Event\EventManager;

class SamplePlugin extends Plugin {

  public function registerEvents() {
    EventManager::register('sample.someevent', function($event) {
      print_r($event->data());
    });
  }

}
```

### Your application bootstrap

```php
<?php

use \Zumba\Symbiosis\Plugin\PluginManager;

// Somewhere in your application bootstrap, load your plugins
PluginManager::loadPlugins(
	'/path/to/your/plugin/directory', // Path to where you stored your plugins
	'YourApp\Plugin'                  // namespace defined in your plugins (see example above)
);
```

### Your application

```php
<?php

use \Zumba\Symbiosis\Event\Event;

// Somewhere in your app, trigger plugins listening to event
$event = new Event('sample.someevent', array('ping' => 'pong'));
$event->trigger();
```

### Output

```shell
Array
(
    [ping] => pong
)
```

## Individual Event Registries

As of `v1.1.6`, event registries have been added to allow for separation of events. This allows for "namespacing"
your event registries. The `EventManager` remains backwards compatible as now the EventManager creates a static instance
of an `EventRegistry`. Since the event structure is loosly coupled in the Plugin architecture, this allows for namespacing
your event registries per plugin.

### Example Event Registry namespacing

```php
<?php

$registry1 = new \Zumba\Symbiosis\Event\EventRegistry();
$registry2 = new \Zumba\Symbiosis\Event\EventRegistry();

$registry1->register('sample.someevent', function ($event) {
	print_r($event->data());
});
$registry2->register('sample.someevent', function ($event) {
	echo "Separate registry\n";
	print_r($event->data());
});

$event = new \Zumba\Symbiosis\Event\Event('sample.someevent', array('ping' => 'pong'));
$registry1->trigger($event);
// Prints: 
// Array(
//   [ping] => pong
// )

$registry2->trigger($event);
// Prints:
// Separate registry
// Array(
//   [ping] => pong
// )
