<?php
/**
 * Symbiosis: a drop-in event driven plugin architecture.
 * Copyright 2012, Zumba Fitness (tm), LLC (http://www.zumba.com)
 *
 * Licensed under The Zumba MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2012, Zumba Fitness (tm), LLC (http://www.zumba.com)
 * @link          https://github.com/zumba/symbiosis
 * @license       Zumba MIT License (http://engineering.zumba.com/licenses/zumba_mit_license.html)
 */
namespace Zumba\Symbiosis\Event;

use \Zumba\Symbiosis\Event\Event;
use \Psr\Log\LoggerInterface;

/**
 * @deprecated Will be removed soon.
 * @see `EventRegistry`
 */
class EventManager
{

    // Backwards compatibility constants.
    const PRIORITY_HIGH = EventRegistry::PRIORITY_HIGH;
    const PRIORITY_MEDIUM = EventRegistry::PRIORITY_MEDIUM;
    const PRIORITY_LOW = EventRegistry::PRIORITY_LOW;

    /**
     * Global event registry.
     *
     * @var \Zumba\Symbiosis\Event\EventRegistry
     */
    protected static $registry;

    /**
     * PSR-3 compliant logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected static $logger;

    /**
     * Set a static logger that will be used for all event registries from this service.
     *
     * @param LoggerInterface $logger
     * @return void
     */
    public static function setLogger(LoggerInterface $logger) : void
    {
        static::$logger = $logger;
    }

    /**
     * Register an event with a callback.
     *
     * Callback should be in form of function(array)
     *
     * @param string|array $events
     * @param callable $callback
     * @return void
     * @throws \Zumba\Symbiosis\Exception\NotCallableException
     */
    public static function register($events, $callback, $priority = 0)
    {
        static::initialize();
        static::$registry->register($events, $callback, $priority);
    }

    /**
     * Call all listeners registered to provided event with data.
     *
     * Returns true if a registered event's callback was called.
     *
     * @param \Zumba\Symbiosis\Event\Event $event Event object
     * @param array $data Data to append/override in the event object
     * @return boolean
     */
    public static function trigger(Event $event, $data = array())
    {
        static::initialize();
        return static::$registry->trigger($event, $data);
    }

    /**
     * Clears all listener callbacks for an event.
     *
     * @param string $event
     * @return void
     */
    public static function clear($event)
    {
        static::initialize();
        static::$registry->clear($event);
    }

    /**
     * Clears all listener callbacks.
     *
     * @return void
     */
    public static function clearAll()
    {
        static::initialize();
        static::$registry->clearAll();
    }

    /**
     * Properly initialize the global event registry.
     *
     * @return void
     */
    protected static function initialize()
    {
        if (!static::$registry instanceof EventRegistry) {
            static::$registry = new EventRegistry(static::$logger);
        }
    }
}
