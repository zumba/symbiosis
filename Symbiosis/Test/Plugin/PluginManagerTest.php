<?php

namespace Symbiosis\Test\Plugin;

use \Symbiosis\Test\TestCase,
	\Symbiosis\Plugin\PluginManager;

/**
 * @group plugin
 */
class PluginManagerTest extends TestCase {

	public function testLoadPluginsAndListing() {
		PluginManager::loadPlugins(__DIR__ . '/SamplePlugins', 'Symbiosis\Test\Plugin\SamplePlugins');
		$pluginList = PluginManager::getPluginList();
		$this->assertCount(2, $pluginList);
		// Confirm priority order
		$expectedList = array(
			'Symbiosis\Test\Plugin\SamplePlugins\TestBPlugin' => 1,
			'Symbiosis\Test\Plugin\SamplePlugins\TestAPlugin' => 2
		);
		$this->assertEquals($expectedList, $pluginList);
	}

}