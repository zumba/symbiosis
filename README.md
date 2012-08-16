Symbiosis is a drop-in event driven plugin architecture.

The goal is to allow anyone to create an event to drop in their application and a plugin interface called by that event.

## Requirements

PHP 5.3+

## Setup

1. In your main application, you need to include the Symbiosis autoloader: `include_once 'symbiosis/autoload.php';`.
1. That's it!

## Testing

After cloning the repo, simply `cd` to the cloned directly and run:

```shell
$ phpunit
```

## Example Plugin

### Plugin

```php
<?php

namespace \YourApp\Plugin;

use \Symbiosis\Framework\Plugin,
    \Symbiosis\Event\EventManager;

class SamplePlugin extends Plugin {

  public function registerEvents() {
    EventManager::register('sample.someevent', function($event) {
      print_r($event->data());
    });
  }

}
```

### Your application

```php
<?php

use \Symbiosis\Plugin\PluginManager,
    \Symbiosis\Event\Event;

// Somewhere in your application bootstrap, load your plugins
PluginManager::loadPlugins(
	'/path/to/your/plugin/directory', // Path to where you stored your plugins
	'YourApp\Plugin'                  // namespace defined in your plugins (see example above)
);

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