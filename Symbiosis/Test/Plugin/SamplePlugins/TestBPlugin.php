<?php

namespace Symbiosis\Test\Plugin\SamplePlugins;

use Symbiosis\Framework\Plugin;

class TestBPlugin extends Plugin {

	public $priority = 1;

	public function registerEvents() {
		// Do nothing
	}

}