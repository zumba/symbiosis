<?php

namespace Zumba\Symbiosis\Framework;

use \Psr\EventDispatcher\StoppableEventInterface;

interface EventInterface extends StoppableEventInterface {

	/**
	 * Get/set the name of this event.
	 */
	public function name(?string $name = null) : string;

	/**
	 * Get/set the data of this event.
	 *
	 * Append option is used to append passed data instead of over-writing.
	 *
	 * @param array $data
	 * @param boolean $append Default no
	 * @return array
	 */
	public function data(?array $data = null, bool $append = false) : array;

	/**
	 * Stops this event from propagating further.
	 *
	 * @return void
	 */
	public function stopPropagation() : void;
}