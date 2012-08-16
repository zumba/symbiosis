<?php

namespace Zumba\Symbiosis\Test\Plugin\SamplePlugins;

use \Zumba\Symbiosis\Framework\Plugin;

class TestDisabledPlugin extends Plugin {

	public $enabled = false;

	public function registerEvents() {
		// Do nothing
	}

}