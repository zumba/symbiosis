<?php

namespace Symbiosis\Plugin;

use \Symbiosis\Framework\Plugin,
	\Symbiosis\Exception;

class PluginManager {

	/**
	 * Holder of all plugin class objects.
	 *
	 * @var array
	 */
	protected static $classObjects;

	/**
	 * Load cart plugins in the cart plugin directory to register events.
	 *
	 * @param string $path Path to plugin directory
	 * @param string $namespace Plugin namespace for plugin classes
	 * @return void
	 */
	public static function loadPlugins($path, $namespace) {
		static::$classObjects = static::buildPluginCache($path, $namespace);
		foreach (static::$classObjects as $plugin) {
			static::initializePlugin($plugin);
		}
	}

	/**
	 * Return a list of plugins available on the path.
	 *
	 * @return array
	 */
	public static function getPluginList() {
		$list = array();
		foreach (static::$classObjects as $plugin) {
			$list[get_class($plugin)] = $plugin->priority;
		}

		return $list;
	}

	/**
	 * Initialize a specific plugin by classname (note: full classname including namespace).
	 *
	 * @param \Symbiosis\Framework\Plugin $plugin Plugin instance.
	 * @return mixed
	 * @throws NoRegisterEventsMethodException
	 */
	public static function initializePlugin(Plugin $plugin) {
		$className = \get_class($plugin);
		// Log plugin initialization (debug)
		if (!method_exists($plugin, 'registerEvents')) {
			throw Exception\NoRegisterEventsMethodException('Unable to call registerEvents method.');
		}

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
		if ($handle = \opendir($path)) {
			while (false !== ($entry = \readdir($handle))) {
				if ($entry === '.' || $entry === '..') {
					continue;
				}
				$class = $namespace . '\\' . \basename($entry, '.php');
				if (class_exists($class)) {
					$classObjects[] = new $class();
				}
			}
			// Order the plugin objects by priority
			usort($classObjects, 'static::comparePriority');
		}

		return $classObjects;
	}

	/**
	 * Compares the priority of two cart plugins.
	 *
	 * @param \Symbiosis\Framework\Plugin $a
	 * @param \Symbiosis\Framework\Plugin $b
	 * @return integer
	 */
	public static function comparePriority(Plugin $a, Plugin $b) {
		if ($a->priority === $b->priority) {
			return 0;
		}
		return ($a->priority < $b->priority) ? - 1 : 1;
	}

}