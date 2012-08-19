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
namespace Zumba\Symbiosis\Test;

use \Zumba\Symbiosis\Log;

class LogTest extends TestCase {

	public function testReceiver() {
		$fake = $this->getMock('stdClass', array('log'));
		$fake->expects($this->once())
			->method('log')
			->with($this->equalTo('Any message'), $this->equalTo(Log::LEVEL_DEBUG));

		Log::receive(array($fake, 'log'));
		Log::write('Any message', Log::LEVEL_DEBUG);
	}

	/**
	 * @expectedException Zumba\Symbiosis\Exception\NotCallableException
	 */
	public function testWrongReceiver() {
		Log::receive('something wrong');
	}

	public function testLevelMinimum() {
		$fake = $this->getMock('stdClass', array('log'));
		Log::receive(array($fake, 'log'));
		Log::minLevel(Log::LEVEL_DEBUG);

		$fake->expects($this->at(0))
			->method('log')
			->with($this->equalTo('Any message'), $this->equalTo(Log::LEVEL_DEBUG));
		Log::write('Any message', Log::LEVEL_DEBUG);

		$fake->expects($this->at(0))
			->method('log')
			->with($this->equalTo('Any message'), $this->equalTo(Log::LEVEL_WARNING));
		Log::write('Any message', Log::LEVEL_WARNING);
	}

	public function testLevelSpecific() {
		$fake = $this->getMock('stdClass', array('log'));
		Log::receive(array($fake, 'log'));
		Log::minLevel(Log::LEVEL_WARNING);

		$fake->expects($this->at(0))
			->method('log')
			->with($this->equalTo('Any message'), $this->equalTo(Log::LEVEL_WARNING));
		Log::write('Any message', Log::LEVEL_WARNING);

		$fake->expects($this->at(0))
			->method('log')
			->with($this->equalTo('Any message'), $this->equalTo(Log::LEVEL_ERROR));
		Log::write('Any message', Log::LEVEL_ERROR);

		$fake->expects($this->never())
			->method('log');
		Log::write('Any message', Log::LEVEL_DEBUG);
	}

	public function testLogDetails() {
		$fake = $this->getMock('stdClass', array('log'));
		Log::receive(array($fake, 'log'));
		Log::minLevel(Log::LEVEL_DEBUG);

		$expectedDetails = array(
			'ping' => 'pong'
		);
		$fake->expects($this->at(0))
			->method('log')
			->with($this->equalTo('Message 1'), $this->equalTo(Log::LEVEL_INFO), $this->equalTo($expectedDetails));
		Log::write('Message 1', Log::LEVEL_INFO, $expectedDetails);
	}

}
