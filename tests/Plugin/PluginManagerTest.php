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
	\Zumba\Symbiosis\Plugin\PluginManager,
	\Zumba\Symbiosis\Event\Event;

/**
 * @group plugin
 */
class PluginManagerTest extends TestCase {

	public function testLoadPluginsAndListing() {
		$pluginManager = new PluginManager(__DIR__ . '/SamplePlugins', 'Zumba\Symbiosis\Test\Plugin\SamplePlugins');
		$pluginManager->loadPlugins();
		$pluginList = $pluginManager->getPluginList();
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
		$pluginManager = new PluginManager('/some/non-existent/directory', 'Test');
		$pluginManager->loadPlugins();
		$pluginList = $pluginManager->getPluginList();
		$this->assertEmpty($pluginList);
	}

	public function testRegisterablePlugins() {
		$pluginManager = new PluginManager(__DIR__ . '/Registerable', 'Zumba\Symbiosis\Test\Plugin\Registerable');
		$pluginManager->loadPlugins();
		$expectedList = [
			'Zumba\Symbiosis\Test\Plugin\Registerable\TestPlugin' => 1
		];
		$this->assertEquals($expectedList, $pluginManager->getPluginList());
		$event1 = new Event('register.1', array('called' => 0));
		$event2 = new Event('register.2', array('called' => 0));
		$pluginManager->trigger($event1);
		$pluginManager->trigger($event2);
		$this->assertEquals(['called' => 1], $event1->data());
		$this->assertEquals(['called' => 2], $event2->data());
	}

	public function testEventSpawner() {
		$pluginManager = new PluginManager('', '');
		$testPlugin = $this->getMock('Zumba\Symbiosis\Test\Plugin\MockablePlugin', ['mockMe']);
		$testPlugin
			->expects($this->once())
			->method('mockMe');
		$pluginManager->initializePlugin($testPlugin);
		$event = $pluginManager->spawnEvent('test', ['var' => 1]);
		$this->assertInstanceOf('Zumba\Symbiosis\Event\Event', $event);
		$this->assertEquals(['var' => 1], $event->data());
		$event->trigger();
	}

	public function testLoadPluginsArrAndListing() {
		$path = array(
			'Zumba\Symbiosis\Test\Plugin\SamplePlugins' => __DIR__ . '/SamplePlugins',
			'Zumba\Symbiosis\Test\Plugin\SamplePlugins\Namespace2' => __DIR__ . '/SamplePlugins/Namespace2',
		);
		$pluginManager = new PluginManager($path);
		$pluginManager->loadPlugins();
		$pluginList = $pluginManager->getPluginList();

		$this->assertCount(5, $pluginList);
		// Confirm priority order
		$expectedList = array(
			'Zumba\Symbiosis\Test\Plugin\SamplePlugins\Namespace2\TestBPlugin' => 1,
			'Zumba\Symbiosis\Test\Plugin\SamplePlugins\TestBPlugin' => 1,
			'Zumba\Symbiosis\Test\Plugin\SamplePlugins\Namespace2\TestAPlugin' => 2,
			'Zumba\Symbiosis\Test\Plugin\SamplePlugins\TestCPlugin' => 2,
			'Zumba\Symbiosis\Test\Plugin\SamplePlugins\TestAPlugin' => 2
		);

		$this->assertEquals($expectedList, $pluginList);
	}

	/**
	 * @expectedException \Exception
	 */
	public function testLoadPluginsArrUsingInt() {
		$pluginManager = new PluginManager(1, '');
	}

	public function testPluginNamespacePathGet() {
		$path = array(
			'Zumba\Symbiosis\Test\Plugin\SamplePlugins' => __DIR__ . '/SamplePlugins',
			'Zumba\Symbiosis\Test\Plugin\SamplePlugins\Namespace2' => __DIR__ . '/SamplePlugins/Namespace2',
		);
		$pluginManager = new PluginManager($path);
		$pathList = $pluginManager->pluginNamespacePath();
		$this->assertCount(2, $pathList);
	}

	public function testPluginNamespacePathSet() {
		$path = array(
			'Zumba\Symbiosis\Test\Plugin\SamplePlugins' => __DIR__ . '/SamplePlugins',
			'Zumba\Symbiosis\Test\Plugin\SamplePlugins\Namespace2' => __DIR__ . '/SamplePlugins/Namespace2',
		);
		$pluginManager = new PluginManager(array());
		$pathList = $pluginManager->pluginNamespacePath($path);
		$this->assertCount(2, $pathList);
	}

	public function testPluginNamespacePathSet2() {
		$path = array(
			'Zumba\Symbiosis\Test\Plugin\SamplePlugins' => __DIR__ . '/SamplePlugins',
			'Zumba\Symbiosis\Test\Plugin\SamplePlugins\Namespace2' => __DIR__ . '/SamplePlugins/Namespace2'
		);
		$pluginManager = new PluginManager($path);
		$oneMore = array('Zumba\Symbiosis\Test\Plugin\SamplePlugins\Namespace3' => __DIR__ . '/SamplePlugins/Namespace3');
		$pathList = $pluginManager->pluginNamespacePath($oneMore);
		$this->assertCount(1, $pathList);
	}
}
