<?php

namespace Symbiosis\Event;

use \Symbiosis\Event\Event,
	\Symbiosis\Exception;

class EventManager {

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
	 * @throws \Symbiosis\Exception\NotCollableException
	 */
	public static function register($events, $callback) {
		if (!is_callable($callback)) {
			throw new Exception\NotCallableException('Registration callback is not callable.');
		}
		$events = (array)$events;
		foreach ($events as $event) {
			static::$registry[$event][] = $callback;
		}
	}

	/**
	 * Call all listeners registered to provided event with data.
	 *
	 * Returns true if a registered event's callback was called.
	 *
	 * @param \Symbiosis\Event\Event $event Event object
	 * @param array $data Data to append/override in the event object
	 * @return boolean
	 */
	public static function trigger(Event $event, $data = array()) {
		$eventName = $event->name();
		$event->data(array_merge($event->data(), $data));
		if (!isset(static::$registry[$eventName])) {
			// Log no event (debug)
			return false;
		}
		// Log trigger event (debug)
		foreach (static::$registry[$eventName] as $listener) {
			if (call_user_func_array($listener, array($event)) === false) {
				$event->stopPropagation();
			}
			if (!$event->isPropagating()) {
				// Log propagation stopped (debug)
				break;
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
		static::$registry = array();
	}

}