<?php
/**
 * Symbiosis: a drop-in event driven plugin architecture.
 * Copyright 2013, Zumba Fitness (tm), LLC (http://www.zumba.com)
 *
 * Licensed under The Zumba MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2013, Zumba Fitness (tm), LLC (http://www.zumba.com)
 * @link          https://github.com/zumba/symbiosis
 * @license       Zumba MIT License (http://engineering.zumba.com/licenses/zumba_mit_license.html)
 */
namespace Zumba\Symbiosis\Test\Event;

use \Zumba\Symbiosis\Test\TestCase,
	\Zumba\Symbiosis\Event\EventRegistry,
	\Zumba\Symbiosis\Event\Event;

/**
 * @group event
 */
class EventRegisryTest extends TestCase {

	protected $registry;

	public static $order = array();

	public function setUp() {
		parent::setUp();
		$this->registry = new EventRegistry();
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->registry);
	}

	public function testRegistrationAndCallback() {
		$testObject = $this->getMock('stdClass', array('testCallback1', 'testCallback2'));
		$testObject->expects($this->once())
			->method('testCallback1')
			->with($this->isInstanceOf('Zumba\Symbiosis\Event\Event'));
		$testObject->expects($this->exactly(2))
			->method('testCallback2')
			->with($this->isInstanceOf('Zumba\Symbiosis\Event\Event'));
		$this->registry->register('test.event1', array($testObject, 'testCallback1'));
		$this->registry->register('test.event1', array($testObject, 'testCallback2'));
		$this->registry->register('test.event2', array($testObject, 'testCallback2'));

		$event1 = new Event('test.event1', array());
		$event2 = new Event('test.event2', array());
		$this->assertTrue($event1->trigger($this->registry));
		$this->assertTrue($event2->trigger($this->registry));
	}

	/**
	 * @expectedException Zumba\Symbiosis\Exception\NotCallableException
	 */
	public function testInvalidRegistration() {
		$obj = new \stdClass();
		$this->registry->register('uncallable', array($obj, 'uncallable'));
	}

	public function testTriggerWithNoListener() {
		$event = new Event('test.event1', array());
		$this->assertFalse($event->trigger($this->registry));
	}

	public function testClearEvent() {
		$testObject = $this->getMock('stdClass', array('testCallback1'));
		$testObject->expects($this->never())
			->method('testCallback1');
		$this->registry->register('test.event1', array($testObject, 'testCallback1'));
		$this->registry->clear('test.event1');
		$event = new Event('test.event1', array());
		$event->trigger($this->registry);
	}

	public function testStopPropagationVarient1() {
		$testObject = $this->getMock('stdClass', array('testCallback1'));
		$testObject->expects($this->never())
			->method('testCallback1');
		$event = new Event('test.event1', array());
		$this->registry->register('test.event1', function(Event $event) {
			return false;
		});
		$this->registry->register('test.event1', array($testObject, 'testCallback1'));
		$this->assertTrue($event->trigger($this->registry));
	}

	public function testStopPropagationVarient2() {
		$testObject = $this->getMock('stdClass', array('testCallback1'));
		$testObject->expects($this->never())
			->method('testCallback1');
		$event = new Event('test.event1', array());
		$this->registry->register('test.event1', function(Event $event) {
			$event->stopPropagation();
		});
		$this->registry->register('test.event1', array($testObject, 'testCallback1'));
		$this->assertTrue($event->trigger($this->registry));
	}

	public function testEventName() {
		$testObject = $this->getMock('stdClass', array('testCallback1'));
		$testObject->expects($this->once())
			->method('testCallback1');
		$this->registry->register('test.event1', array($testObject, 'testCallback1'));
		$event = new Event('test.event1', array());
		$event->trigger($this->registry);
		$event->name('test.event2');
		$event->trigger($this->registry);
	}

	public function testSuggestingPreventAction() {
		$event = new Event('test.event1', array());
		$this->registry->register('test.event1', function(Event $event) {
			$event->preventAction();
		});
		$event->trigger($this->registry);
		$this->assertTrue($event->shouldPreventAction());
	}

	public function testSuggestingPreventActionWithMessage() {
		$event = new Event('test.event1', array());
		$this->registry->register('test.event1', function(Event $event) {
			$event->preventAction(true, 'Reason why should prevent.');
		});
		$event->trigger($this->registry);
		$this->assertTrue($event->shouldPreventAction());
		$this->assertEquals('Reason why should prevent.', $event->preventActionMessage());
	}

	public function testEventPriority() {
		$event = new Event('test.event1', array());
		// lower priority
		$lowPriority = function(Event $event) {
			$data = $event->data();
			$data['order'][] = 3;
			$event->data($data);
		};
		$highPriority = function(Event $event) {
			$data = $event->data();
			$data['order'][] = 1;
			$event->data($data);
		};
		$alsoHighPriority = function(Event $event) {
			$data = $event->data();
			$data['order'][] = 2;
			$event->data($data);
		};
		$this->registry->register('test.event1', $lowPriority, EventRegistry::PRIORITY_LOW);
		$this->registry->register('test.event1', $highPriority, EventRegistry::PRIORITY_HIGH);
		$this->registry->register('test.event1', $alsoHighPriority, EventRegistry::PRIORITY_HIGH);
		$event = new Event('test.event1', array('order' => array()));
		$this->registry->trigger($event);
		$data = $event->data();
		$this->assertEquals(array(1, 2, 3), $data['order']);
	}

}
