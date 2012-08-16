<?php

namespace Symbiosis\Test;

use \Symbiosis\Log;

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
	 * @expectedException Symbiosis\Exception\NotCallableException
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

}
