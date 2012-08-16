<?php

namespace Symbiosis;

class AutoLoader {

	/**
	 * Self Instance
	 *
	 * @var \Symbiosis\AutoLoader
	 */
	protected static $self;

	/**
	 * Relation of namespaces and folders
	 *
	 * @var array
	 */
	protected $namespaces = array();

	/**
	 * Constructor
	 */
	protected function __construct() {
		\spl_autoload_register(array($this, 'loadClass'));
	}

	/**
	 * Get Instance
	 *
	 * @return \Symbiosis\AutoLoader
	 */
	protected static function getInstance() {
		if (!static::$self) {
			static::$self = new static();
		}
		return static::$self;
	}

	/**
	 * Try to load an specific class
	 *
	 * @param string $classname
	 * @return boolean
	 */
	public function loadClass($classname) {
		$classname = ltrim($classname, '\\');
		foreach ($this->namespaces as $ns => $path) {
			if (strpos($classname, $ns) === 0) {
				$path .= str_replace('\\', DIRECTORY_SEPARATOR, $classname) . '.php';
				if (!file_exists($path)) {
					return false;
				}
				return include $path;
			}
		}
		return false;
	}

	/**
	 * Register the autoload to specific namespace
	 *
	 * @param string $namespace
	 * @param string $path
	 * @return void
	 */
	public static function register($namespace, $path) {
		$_this = static::getInstance();
		$_this->namespaces[trim($namespace, '\\') . '\\'] = \rtrim($path, '/\\ ') . DIRECTORY_SEPARATOR;
	}

}