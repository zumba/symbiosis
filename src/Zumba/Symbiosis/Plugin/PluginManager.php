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
namespace Zumba\Symbiosis\Plugin;

use \Zumba\Symbiosis\Framework\Plugin,
	\Zumba\Symbiosis\Log,
	\Zumba\Symbiosis\Exception;

class PluginManager {

	/**
	 * Path location to plugin directory.
	 *
	 * @var string
	 */
	protected $path = '';

	/**
	 * Plugin namespace to be observed.
	 *
	 * @var string
	 */
	protected $namespace = '';

	/**
	 * Holder of all plugin class objects.
	 *
	 * @var array
	 */
	protected $classObjects = array();

	/**
	 * Constructor.
	 * 
	 * @param string $path Plugin file path.
	 * @param string $namespace Plugin namespace.
	 */
	public function __construct($path, $namespace) {
		$this->path = $path;
		$this->namespace = $namespace;
	}

	/**
	 * Get/set the plugin path.
	 * 
	 * @param string $path
	 * @return string
	 */
	public function path($path = null) {
		if ($path !== null) {
			$this->path = $path;
		}
		return $this->path;
	}

	/**
	 * Get/set the plugin namespace;
	 *
	 * @param string $namespace
	 * @return string
	 */
	public function pluginNamespace($namespace = null) {
		if ($namespace !== null) {
			$this->namespace = $namespace;
		}
		return $this->namespace;
	}

	/**
	 * Load cart plugins in the cart plugin directory to register events.
	 *
	 * @return void
	 */
	public function loadPlugins() {
		$objects = $this->buildPluginCache();
		foreach ($objects as $plugin) {
			$this->initializePlugin($plugin);
		}
		$this->classObjects += $objects;
	}

	/**
	 * Return a list of plugins available on the path.
	 *
	 * @return array
	 */
	public function getPluginList() {
		$list = array();
		foreach ($this->classObjects as $classname => $plugin) {
			$list[$classname] = $plugin->priority;
		}

		return $list;
	}

	/**
	 * Initialize a specific plugin by classname (note: full classname including namespace).
	 *
	 * @param \Zumba\Symbiosis\Framework\Plugin $plugin Plugin instance.
	 * @return mixed
	 * @throws \Zumba\Symbiosis\Exception\NoRegisterEventsMethodException
	 */
	public function initializePlugin(Plugin $plugin) {

		return $plugin->registerEvents();
	}

	/**
	 * Builds the plugin cache and gets an array of plugin objects.
	 *
	 * @return array
	 */
	protected function buildPluginCache() {
		$classObjects = array();
		if (!is_dir($this->path)) {
			Log::write('Plugin path not a directory.', Log::LEVEL_WARNING, compact('path'));
			return array();
		}
		if ($handle = opendir($this->path)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry === '.' || $entry === '..') {
					continue;
				}
				$class = $this->namespace . '\\' . basename($entry, '.php');
				if (class_exists($class)) {
					$plugin = new $class();
					if ($plugin->enabled) {
						$classObjects[$class] = $plugin;
					}
				}
			}
			closedir($handle);
			// Order the plugin objects by priority
			uasort($classObjects, array($this, 'comparePriority'));
		}

		return $classObjects;
	}

	/**
	 * Compares the priority of two cart plugins.
	 *
	 * @param \Zumba\Symbiosis\Framework\Plugin $a
	 * @param \Zumba\Symbiosis\Framework\Plugin $b
	 * @return integer
	 */
	protected function comparePriority(Plugin $a, Plugin $b) {
		if ($a->priority === $b->priority) {
			return 0;
		}
		return ($a->priority < $b->priority) ? - 1 : 1;
	}

}
