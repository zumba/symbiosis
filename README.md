Symbiosis is a drop-in event driven plugin architecture.

The goal is to allow anyone to create a plugin structure for their existing code base using an event oriented system.
The secondary benefit of using Symbiosis is that the event structure can be used apart from the plugin structure.

__Current Version__: v1.1.4

[![Build Status](https://secure.travis-ci.org/zumba/symbiosis.png)](http://travis-ci.org/zumba/symbiosis)

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
