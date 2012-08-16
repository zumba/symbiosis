<?php

namespace Symbiosis\Test\Plugin\SamplePlugins;

use Symbiosis\Framework\Plugin;

class TestAPlugin extends Plugin {

	public $priority = 2;

	public function registerEvents() {
		// Do nothing
	}

}