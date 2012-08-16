<?php

namespace Symbiosis\Event;

use \Symbiosis\Event\EventManager;

class Event {

	/**
	 * Name of event.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Data to pass to event listeners.
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Is this event currently being processed by listeners?
	 *
	 * @var boolean
	 */
	protected $isPropagating = true;

	/**
	 * Constructor.
	 *
	 * @param string $name
	 * @param array $data
	 */
	public function __construct($name, $data = array()) {
		$this->name = $name;
		$this->data = $data;
	}

	/**
	 * Get/set the name of this event.
	 *
	 * @param string $name
	 * @return string
	 */
	public function name($name = null) {
		if ($name !== null) {
			$this->name = $name;
		}
		return $this->name;
	}

	/**
	 * Get/set the data of this event.
	 *
	 * @param array $data
	 * @return array
	 */
	public function data($data = null) {
		if ($data !== null) {
			$this->data = $data;
		}
		return $this->data;
	}

	/**
	 * Is this event propagating?
	 *
	 * @return boolean
	 */
	public function isPropagating() {
		return $this->isPropagating;
	}

	/**
	 * Stops this event from propagating futher.
	 *
	 * @return void
	 */
	public function stopPropagation() {
		$this->isPropagating = false;
	}

	/**
	 * Convenience method for triggering the event from the event object.
	 *
	 * @return boolean
	 */
	public function trigger() {
		return EventManager::trigger($this);
	}

}