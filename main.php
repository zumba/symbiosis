<?php

if (!defined('SYMBIOSIS_ROOT')) {
	define('SYMBIOSIS_ROOT', __DIR__);
}
if (!defined('SYMBIOSIS_CORE')) {
	define('SYMBIOSIS_CORE', SYMBIOSIS_ROOT . '/Symbiosis/');
}

require SYMBIOSIS_CORE . '/AutoLoader.php';
\Symbiosis\AutoLoader::register('Symbiosis', SYMBIOSIS_ROOT);