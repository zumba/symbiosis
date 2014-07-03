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
	\Zumba\Symbiosis\Framework\Registerable,
	\Zumba\Symbiosis\Framework\OpenEndable,
	\Zumba\Symbiosis\Event\EventRegistry,
	\Zumba\Symbiosis\Event\EventManager,
	\Zumba\Symbiosis\Event\Event,
	\Zumba\Symbiosis\Log,
	\Zumba\Symbiosis\Exception;

class PluginManager {

	/**
	 * Constant for directory separator - note different from PHP's own constant
	 */
	const DIR_SEPARATOR = '\\';

	/**
	 * An array with a set of namespace => path
	 *
	 * @var array
	 */
	protected $path = '';

	/**
	 * Plugin namespace to be observed.
	 *
	 * @var string
	 * @deprecated
	 */
	protected $namespace = null;

	/**
	 * Holder of all plugin class objects.
	 *
	 * @var array
	 */
	protected $classObjects = array();

	/**
	 * PluginManager event context.
	 *
	 * @var Zumba\Symbiosis\Plugin\EventRegistry
	 */
	protected $context;

	/**
	 * Constructor.
	 * 
	 * @param array $path Plugin file path. An array where the namespace is the key and the path is the value
	 * @param string $namespace Plugin namespace. Deprecated
	 */
	public function __construct($path, $namespace = '') {
		$this->path = (!is_string($path)) ? (array)$path : array($namespace => $path);
	}

	/**
	 * Get/set the plugin path.
	 * 
	 * @param string $path
	 * @return string
	 * @deprecated
	 */
	public function path($path = null) {
		if ($path !== null) {
			$this->path = !is_string($path) ? array_shift($path) : $path;
		}
		return $this->path;
	}

	/**
	 * Get/set the plugin namespace;
	 *
	 * @param string $namespace
	 * @return string
	 * @deprecated
	 */
	public function pluginNamespace($namespace = null) {
		if ($namespace !== null) {
			$this->namespace = !is_string($this->path) ? null : $namespace;
		}

		if (!is_string($this->path)) {
			reset($this->path);
			$this->namespace = key($this->path);
		}
		return $this->namespace;
	}

	/**
	 * Will set/get to the plugin path / namespace array or retrive it's items
	 *
	 * @param array $pluginArr An array where the key is the namespace and the path is value
	 * @return array
	 */
	public function pluginNamespacePath($pluginArr = array()) {
		if (is_array($pluginArr) && !empty($pluginArr)) {
			$this->path = $pluginArr;
		}
		return $this->path;
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
	 * Initialize a specific plugin and binds its events.
	 *
	 * @param \Zumba\Symbiosis\Framework\Plugin $plugin Plugin instance.
	 * @return mixed
	 * @throws \Zumba\Symbiosis\Exception\NoRegisterEventsMethodException
	 */
	public function initializePlugin(Plugin $plugin) {
		Log::write('Initializing plugin.', Log::LEVEL_DEBUG, array('classname' => (string)$plugin));
		if ($plugin instanceof Registerable) {
			$plugin->eventContext($this->getContext());
			return $plugin->bindPluginEvents();
		} elseif ($plugin instanceof OpenEndable) {
			return $plugin->registerEvents();
		}
		Log::write('No plugin strategy implemented.', Log::LEVEL_WARNING, array('classname' => (string)$plugin));
		return false;
	}

	/**
	 * Get an instance of the event context for this plugin manager.
	 *
	 * @return Zumba\Symbiosis\Plugin\EventRegistry
	 */
	public function getContext() {
		if (!$this->context instanceof EventRegistry) {
			$this->context = new EventRegistry();
		}
		return $this->context;
	}

	/**
	 * Create a new event that has this plugin as its context.
	 *
	 * @param string $name
	 * @param array $data
	 * @return Zumba\Symbiosis\Event\Event
	 */
	public function spawnEvent($name, $data = array()) {
		return (new Event($name, $data))->setPluginContext($this);
	}

	/**
	 * Trigger an event to the bound context of this plugin manager.
	 *
	 * @param Zumba\Symbiosis\Event\Event $event
	 * @param array $data
	 * @return boolean
	 */
	public function trigger(Event $event, $data = array()) {
		if (!$this->context instanceof EventRegistry) {
			return EventManager::trigger($event, $data);
		}
		return $this->context->trigger($event, $data);
	}

	/**
	 * Builds the plugin cache and gets an array of plugin objects.
	 *
	 * @return array
	 */
	protected function buildPluginCache() {
		$classObjects = array();

		if (!is_array($this->path)) {
			return $classObjects;
		}

		foreach ($this->path as $namespace => $path) {
			if (!is_string($path) || !is_string($namespace)) {
				// avoiding numeric indexes or values
				Log::write('Either the path or the namespace is not correctly set.', Log::LEVEL_WARNING, compact('path', 'namespace'));
				continue;
			}

			if (!is_dir($path)) {
				Log::write('Plugin path not a directory.', Log::LEVEL_WARNING, compact('path'));
				continue;
			}

			try {
				$iterator = new \DirectoryIterator($path);
				foreach ($iterator as $dirInfo) {
					if ($dirInfo->isDot() || !$dirInfo->isFile()) {
						continue;
					}
	
					$class = $namespace . static::DIR_SEPARATOR . $dirInfo->getBasename('.php');
					if (class_exists($class)) {
						$plugin = new $class();
						if ($plugin->enabled) {
							$classObjects[$class] = $plugin;
						}
					}
				}

				if (!empty($classObjects)) {
					uasort($classObjects, array($this, 'comparePriority'));
				}
			} catch (\Exception $e) {
				Log::write('Exception created while building cache.', Log::LEVEL_ERROR, array(
					'message' => $e->getMessage(),
					'path' => $path,
					'namespace' => $namespace
				));
			}
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