<?php

namespace Zumba\Symbiosis\Test\Plugin\SamplePlugins;

use \Zumba\Symbiosis\Framework\Plugin;

class TestBPlugin extends Plugin {

	public $priority = 1;

	public function registerEvents() {
		// Do nothing
	}

}