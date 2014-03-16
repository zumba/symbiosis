<?php

namespace Zumba\Symbiosis\Framework;

interface Registerable {

	/**
	 * Return a hash array containing the event name as the key and a callback or array of callbacks as the value.
	 * 
	 * @return array
	 */
	public function getEvents();

}
