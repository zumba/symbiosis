<?php

namespace Zumba\Symbiosis\Event;

use \Zumba\Symbiosis\Event\Event,
	\Zumba\Symbiosis\Log,
	\Zumba\Symbiosis\Exception;

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
	 * @throws \Zumba\Symbiosis\Exception\NotCollableException
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
	 * @param \Zumba\Symbiosis\Event\Event $event Event object
	 * @param array $data Data to append/override in the event object
	 * @return boolean
	 */
	public static function trigger(Event $event, $data = array()) {
		$eventName = $event->name();
		$event->data(array_merge($event->data(), $data));
		if (!isset(static::$registry[$eventName])) {
			Log::write('no event', Log::LEVEL_DEBUG);
			return false;
		}
		Log::write('trigger event', Log::LEVEL_DEBUG);
		foreach (static::$registry[$eventName] as $listener) {
			if (call_user_func_array($listener, array($event)) === false) {
				$event->stopPropagation();
			}
			if (!$event->isPropagating()) {
				Log::write('propagation stopped', Log::LEVEL_DEBUG);
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