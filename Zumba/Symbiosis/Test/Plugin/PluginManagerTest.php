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

	/**
	 * @runInSeparateProcess
	 */
	public function testNonExistentPluginDirectory() {
		PluginManager::loadPlugins('/some/non-existent/directory', 'Test');
		$pluginList = PluginManager::getPluginList();
		$this->assertEmpty($pluginList);
	}

}