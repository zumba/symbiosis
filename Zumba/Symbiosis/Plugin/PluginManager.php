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
	 * Holder of all plugin class objects.
	 *
	 * @var array
	 */
	protected static $classObjects = array();

	/**
	 * Load cart plugins in the cart plugin directory to register events.
	 *
	 * @param string $path Path to plugin directory
	 * @param string $namespace Plugin namespace for plugin classes
	 * @return void
	 */
	public static function loadPlugins($path, $namespace) {
		$objects = static::buildPluginCache($path, $namespace);
		foreach ($objects as $plugin) {
			static::initializePlugin($plugin);
		}
		static::$classObjects += $objects;
	}

	/**
	 * Return a list of plugins available on the path.
	 *
	 * @return array
	 */
	public static function getPluginList() {
		$list = array();
		foreach (static::$classObjects as $classname => $plugin) {
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
	public static function initializePlugin(Plugin $plugin) {
		$className = \get_class($plugin);
		Log::write('Initializing plugin.', Log::LEVEL_DEBUG, compact('className'));

		return $plugin->registerEvents();
	}

	/**
	 * Builds the plugin cache and gets an array of plugin objects.
	 *
	 * @param string $path Override path for where to look for plugin files.
	 * @return array
	 */
	protected static function buildPluginCache($path, $namespace) {
		$classObjects = array();
		if (!is_dir($path)) {
			Log::write('Plugin path not a directory.', Log::LEVEL_WARNING, compact('path'));
			return array();
		}
		if ($handle = \opendir($path)) {
			while (false !== ($entry = \readdir($handle))) {
				if ($entry === '.' || $entry === '..') {
					continue;
				}
				$class = $namespace . '\\' . \basename($entry, '.php');
				if (class_exists($class)) {
					$plugin = new $class();
					if ($plugin->enabled) {
						$classObjects[$class] = $plugin;
					}
				}
			}
			closedir($handle);
			// Order the plugin objects by priority
			uasort($classObjects, 'static::comparePriority');
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
	public static function comparePriority(Plugin $a, Plugin $b) {
		if ($a->priority === $b->priority) {
			return 0;
		}
		return ($a->priority < $b->priority) ? - 1 : 1;
	}

}