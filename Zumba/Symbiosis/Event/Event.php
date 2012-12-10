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

use \Zumba\Symbiosis\Event\EventManager;

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
	 * Suggestion to application and other plugins if further action should be halted.
	 *
	 * @var boolean
	 */
	protected $shouldPreventAction = false;

	/**
	 * Reason to suggest to the application to stop further action.
	 *
	 * @var string
	 */
	protected $preventActionMessage = '';

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
	 * Determines if it is suggested to prevent further actions.
	 *
	 * @return boolean
	 */
	public function shouldPreventAction() {
		return $this->shouldPreventAction;
	}

	/**
	 * Gets the reason to prevent further actions.
	 *
	 * @return string
	 */
	public function preventActionMessage() {
		return $this->preventActionMessage;
	}

	/**
	 * Sets the suggestion of preventing further action.  Default true.
	 *
	 * @param boolean $prevent
	 * @param string $message
	 * @return void
	 */
	public function preventAction($prevent = true, $message = '') {
		$this->shouldPreventAction = (boolean)$prevent;
		$this->preventActionMessage = (string)$message;
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