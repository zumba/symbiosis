<?php

namespace Zumba\Symbiosis\Framework;

abstract class Plugin {

	const PRIORITY_DEFAULT = 100;

	/**
	 * Determines the order in which the plugins are executed for a specific event.
	 *
	 * @var integer
	 */
	public $priority = self::PRIORITY_DEFAULT;

	/**
	 * Determines if this plugin should be included as activated.
	 *
	 * @var boolean
	 */
	public $enabled = true;

	/**
	 * Constructor.
	 */
	public function __construct() {
	}

	/**
	 * Main method called when plugin is initialized to register events to listen.
	 *
	 * @return void
	 */
	abstract public function registerEvents();

}