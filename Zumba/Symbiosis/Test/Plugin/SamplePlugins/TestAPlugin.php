<?php

namespace Zumba\Symbiosis\Test\Plugin\SamplePlugins;

use \Zumba\Symbiosis\Framework\Plugin;

class TestAPlugin extends Plugin {

	public $priority = 2;

	public function registerEvents() {
		// Do nothing
	}

}