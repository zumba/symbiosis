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