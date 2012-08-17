<?php

namespace Zumba\Symbiosis\Test\Plugin;

use \Zumba\Symbiosis\Test\TestCase,
	\Zumba\Symbiosis\Plugin\PluginManager;

/**
 * @group plugin
 */
class PluginManagerTest extends TestCase {

	public function testLoadPluginsAndListing() {
		PluginManager::loadPlugins(__DIR__ . '/SamplePlugins', 'Zumba\Symbiosis\Test\Plugin\SamplePlugins');
		$pluginList = PluginManager::getPluginList();
		$this->assertCount(3, $pluginList);
		// Confirm priority order
		$expectedList = array(
			'Zumba\Symbiosis\Test\Plugin\SamplePlugins\TestBPlugin' => 1,
			'Zumba\Symbiosis\Test\Plugin\SamplePlugins\TestAPlugin' => 2,
			'Zumba\Symbiosis\Test\Plugin\SamplePlugins\TestCPlugin' => 2
		);
		$this->assertEquals($expectedList, $pluginList);
	}

}