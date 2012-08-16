<?php

namespace Symbiosis\Framework;

abstract class Plugin {

	const PRIORITY_DEFAULT = 100;

	public $priority = 100;

	public function __construct() {
	}

	abstract public function registerEvents();

}