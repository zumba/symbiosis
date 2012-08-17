<?php

namespace Zumba\Symbiosis\Test\Plugin\SamplePlugins;

use \Zumba\Symbiosis\Framework\Plugin;

class TestCPlugin extends Plugin {

	public $priority = 2; // Same priority as TestAPlugin

	public function registerEvents() {
		// Do nothing
	}

}