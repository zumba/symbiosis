<?php
/**
 * Symbiosis: a drop-in event driven plugin architecture.
 * Copyright 2013, Zumba Fitness (tm), LLC (http://www.zumba.com)
 *
 * Licensed under The Zumba MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2013, Zumba Fitness (tm), LLC (http://www.zumba.com)
 * @link          https://github.com/zumba/symbiosis
 * @license       Zumba MIT License (http://engineering.zumba.com/licenses/zumba_mit_license.html)
 */
namespace Zumba\Symbiosis\Event;

use \Zumba\Symbiosis\Log;
use \Zumba\Symbiosis\Exception;
use \Zumba\Symbiosis\Framework\EventInterface;
use \Psr\EventDispatcher\ListenerProviderInterface;
use \Psr\EventDispatcher\EventDispatcherInterface;

class EventRegistry implements ListenerProviderInterface, EventDispatcherInterface
{

    // Constant event priorities
    const PRIORITY_HIGH = 0;
    const PRIORITY_MEDIUM = 1;
    const PRIORITY_LOW = 2;

    /**
     * Container of registered events for this registry.
     *
     * @var array
     */
    protected $registry = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
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
    public function register($events, $callback, $priority = 0)
    {
        if (!is_callable($callback)) {
            throw new Exception\NotCallableException('Registration callback is not callable.');
        }
        $events = (array)$events;
        foreach ($events as $event) {
            $this->registry[$event][$priority][] = $callback;
        }
        ksort($this->registry[$event]);
    }

    /**
     * Call all listeners registered to provided event with data.
     *
     * Returns true if a registered event's callback was called.
     *
     * @param \Zumba\Symbiosis\Framework\EventInterface $event Event object
     * @param array $data Data to append/override in the event object
     * @return boolean
     * @deprecated See dispatch()
     */
    public function trigger(EventInterface $event, $data = array())
    {
        $eventName = $event->name();
        $event->data(array_merge($event->data(), $data));
        if (!isset($this->registry[$eventName])) {
            Log::write('No event registered.', Log::LEVEL_DEBUG, compact('eventName'));
            return false;
        }
        Log::write('Event triggered.', Log::LEVEL_DEBUG, compact('eventName'));
        foreach ($this->getListenersForEvent($event) as $listener) {
            if ($listener($event) === false) {
                $event->stopPropagation();
            }
            if ($event->isPropagationStopped()) {
                Log::write('Propagation stopped.', Log::LEVEL_DEBUG, compact('listener', 'eventName'));
                break;
            }
        }
        $event->stopPropagation();
        return true;
    }

    /**
     * Provide all relevant listeners with an event to process.
     *
     * @param object $event
     *   The object to process.
     *
     * @return object
     *   The Event that was passed, now modified by listeners.
     * @throws \Zumba\Symbiosis\Exception\NotRetrievableException
     */
    public function dispatch(object $event)
    {
        if (!$event instanceof EventInterface) {
            throw new Exception\NotRetrievableException('Passed object must implement `NamableEventInterface` for registry identification.');
        }
        $this->trigger($event);
        return $event;
    }

    /**
     * @param object $event
     *   An event for which to return the relevant listeners.
     * @return iterable[callable]
     *   An iterable (array, iterator, or generator) of callables.  Each
     *   callable MUST be type-compatible with $event.
     * @throws \Zumba\Symbiosis\Exception\NotRetrievableException
     */
    public function getListenersForEvent(object $event) : iterable
    {
        if (!$event instanceof EventInterface) {
            throw new Exception\NotRetrievableException('Passed object must implement `NamableEventInterface` for registry identification.');
        }
        foreach ($this->registry[$event->name()] as $listeners) {
            foreach ($listeners as $listener) {
                yield $listener;
            }
        }
    }

    /**
     * Clears all listener callbacks for an event.
     *
     * @param string $event
     * @return void
     */
    public function clear($event)
    {
        Log::write('Clearing individual event.', Log::LEVEL_DEBUG, compact('event'));
        if (isset($this->registry[$event])) {
            unset($this->registry[$event]);
        }
    }

    /**
     * Clears all listener callbacks.
     *
     * @return void
     */
    public function clearAll()
    {
        Log::write('Clearing all events.', Log::LEVEL_DEBUG);
        $this->registry = array();
    }
}
