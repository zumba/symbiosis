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
namespace Zumba\Symbiosis;

use \Zumba\Symbiosis\Exception\NotCallableException;

class Log {

	const LEVEL_OFF = 0;
	const LEVEL_DEBUG = 2;
	const LEVEL_INFO = 4;
	const LEVEL_WARNING = 8;
	const LEVEL_ERROR = 16;

	/**
	 * Receiver method of the log messages
	 *
	 * @var callable
	 */
	protected static $receiver;

	/**
	 * Minimum level to transmit the logs
	 *
	 * @var integer
	 */
	protected static $minLevel = self::LEVEL_DEBUG;

	/**
	 * Set the method that will receive the log message and level
	 *
	 * @param callable $callable Callable to receive the message. It will receive 2 parameters: log message, log level
	 * @return void
	 */
	public static function receive($callable) {
		if (!is_callable($callable)) {
			throw new NotCallableException('The parameter must be a callable');
		}
		static::$receiver = $callable;
	}

	/**
	 * Set the minimum level to receive log messages
	 *
	 * @param integer $level See LEVEL_* constants. If null, will return the current setup
	 * @return integer
	 */
	public static function minLevel($level = null) {
		if (is_int($level)) {
			static::$minLevel = $level;
		}
		return static::$minLevel;
	}

	/**
	 * Write a log message
	 *
	 * @param string $message Log message
	 * @param integer $level Log level. See LEVEL_* constants
	 * @param array $details Further details to provide to the logger
	 * @return void
	 */
	public static function write($message, $level, $details = array()) {
		$receiver = static::$receiver;
		if (!$receiver) {
			return;
		}

		if ($level < static::$minLevel) {
			return;
		}

		call_user_func($receiver, $message, $level, $details);
	}

}
