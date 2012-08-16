<?php

namespace Zumba\Symbiosis\Framework;

abstract class Plugin {

	const PRIORITY_DEFAULT = 100;

	public $priority = 100;

	public function __construct() {
	}

	/**
	 * Main method called when plugin is initialized to register events to listen.
	 *
	 * @return void
	 */
	abstract public function registerEvents();

}