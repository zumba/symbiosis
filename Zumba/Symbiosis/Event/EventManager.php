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

use \Zumba\Symbiosis\Event\Event as SymbiosisEvent,
	\Zumba\Symbiosis\Log,
	\Zumba\Symbiosis\Exception;

class EventManager {

	// Constant event priorities
	const PRIORITY_HIGH = 0;
	const PRIORITY_MEDIUM = 1;
	const PRIORITY_LOW = 2;

	/**
	 * Container of all event registrations.
	 *
	 * @var array
	 */
	protected static $registry = array();

	/**
	 * Register an event with a callback.
	 *
	 * Callback should be in form of function(array)
	 *
	 * @param string|array $events
	 * @param callable $callback
	 * @return void
	 * @throws \Zumba\Symbiosis\Exception\NotCollableException
	 */
	public static function register($events, $callback, $priority = 0) {
		if (!is_callable($callback)) {
			throw new Exception\NotCallableException('Registration callback is not callable.');
		}
		$events = (array)$events;
		foreach ($events as $event) {
			static::$registry[$event][$priority][] = $callback;
		}
		ksort(static::$registry[$event]);
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
	public static function trigger(SymbiosisEvent $event, $data = array()) {
		$eventName = $event->name();
		$event->data(array_merge($event->data(), $data));
		if (!isset(static::$registry[$eventName])) {
			Log::write('No event registered.', Log::LEVEL_DEBUG, compact('eventName'));
			return false;
		}
		Log::write('Event triggered.', Log::LEVEL_DEBUG, compact('eventName'));
		foreach (static::$registry[$eventName] as $listeners) {
			foreach ($listeners as $listener) {
				if (call_user_func_array($listener, array($event)) === false) {
					$event->stopPropagation();
				}
				if (!$event->isPropagating()) {
					Log::write('Propagation stopped.', Log::LEVEL_DEBUG, compact('listener', 'eventName'));
					break;
				}
			}
		}
		return true;
	}

	/**
	 * Clears all listener callbacks for an event.
	 *
	 * @param string $event
	 * @return void
	 */
	public static function clear($event) {
		Log::write('Clearing individual event.', Log::LEVEL_DEBUG, compact('event'));
		if (isset(static::$registry[$event])) {
			unset(static::$registry[$event]);
		}
	}

	/**
	 * Clears all listener callbacks.
	 *
	 * @return void
	 */
	public static function clearAll() {
		Log::write('Clearing all events.', Log::LEVEL_DEBUG);
		static::$registry = array();
	}

}