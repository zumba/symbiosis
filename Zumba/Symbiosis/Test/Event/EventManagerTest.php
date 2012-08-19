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
namespace Zumba\Symbiosis\Test\Event;

use \Zumba\Symbiosis\Test\TestCase,
	\Zumba\Symbiosis\Event\EventManager,
	\Zumba\Symbiosis\Event\Event;

/**
 * @group event
 */
class EventManagerTest extends TestCase {

	public function tearDown() {
		parent::tearDown();
		EventManager::clearAll();
	}

	public function testRegistrationAndCallback() {
		$testObject = $this->getMock('stdClass', array('testCallback1', 'testCallback2'));
		$testObject->expects($this->once())
			->method('testCallback1')
			->with($this->isInstanceOf('Zumba\Symbiosis\Event\Event'));
		$testObject->expects($this->exactly(2))
			->method('testCallback2')
			->with($this->isInstanceOf('Zumba\Symbiosis\Event\Event'));
		EventManager::register('test.event1', array($testObject, 'testCallback1'));
		EventManager::register('test.event1', array($testObject, 'testCallback2'));
		EventManager::register('test.event2', array($testObject, 'testCallback2'));

		$event1 = new Event('test.event1');
		$event2 = new Event('test.event2');
		$this->assertTrue($event1->trigger());
		$this->assertTrue($event2->trigger());
	}

	/**
	 * @expectedException Zumba\Symbiosis\Exception\NotCallableException
	 */
	public function testInvalidRegistration() {
		$obj = new \stdClass();
		EventManager::register('uncallable', array($obj, 'uncallable'));
	}

	public function testTriggerWithNoListener() {
		$event = new Event('test.event1');
		$this->assertFalse($event->trigger());
	}

	public function testClearEvent() {
		$testObject = $this->getMock('stdClass', array('testCallback1'));
		$testObject->expects($this->never())
			->method('testCallback1');
		EventManager::register('test.event1', array($testObject, 'testCallback1'));
		EventManager::clear('test.event1');
		$event = new Event('test.event1');
		$event->trigger();
	}

	public function testStopPropagationVarient1() {
		$testObject = $this->getMock('stdClass', array('testCallback1'));
		$testObject->expects($this->never())
			->method('testCallback1');
		$event = new Event('test.event1');
		EventManager::register('test.event1', function(Event $event) {
			return false;
		});
		EventManager::register('test.event1', array($testObject, 'testCallback1'));
		$this->assertTrue($event->trigger());
	}

	public function testStopPropagationVarient2() {
		$testObject = $this->getMock('stdClass', array('testCallback1'));
		$testObject->expects($this->never())
			->method('testCallback1');
		$event = new Event('test.event1');
		EventManager::register('test.event1', function(Event $event) {
			$event->stopPropagation();
		});
		EventManager::register('test.event1', array($testObject, 'testCallback1'));
		$this->assertTrue($event->trigger());
	}

	public function testEventName() {
		$testObject = $this->getMock('stdClass', array('testCallback1'));
		$testObject->expects($this->once())
			->method('testCallback1');
		EventManager::register('test.event1', array($testObject, 'testCallback1'));
		$event = new Event('test.event1');
		$event->trigger();
		$event->name('test.event2');
		$event->trigger();
	}

	public function testSuggestingPreventAction() {
		$event = new Event('test.event1');
		EventManager::register('test.event1', function(Event $event) {
			$event->preventAction();
		});
		$event->trigger();
		$this->assertTrue($event->shouldPreventAction());
	}

}