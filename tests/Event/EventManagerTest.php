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

use \Zumba\Symbiosis\Test\TestCase;
use \Zumba\Symbiosis\Event\EventManager;
use \Zumba\Symbiosis\Event\EventRegistry;
use \Zumba\Symbiosis\Event\Event;

/**
 * @group event
 */
class EventManagerTest extends TestCase
{
    public static $order = array();

    public function tearDown() : void
    {
        parent::tearDown();
        EventManager::clearAll();
    }

    public function testRegistrationAndCallback()
    {
        $testObject = $this->getMockBuilder('stdClass')->setMethods(['testCallback1', 'testCallback2'])->getMock();
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

    public function testInvalidRegistration()
    {
        $this->expectException('Zumba\Symbiosis\Exception\NotCallableException');
        $obj = new \stdClass();
        EventManager::register('uncallable', array($obj, 'uncallable'));
    }

    public function testTriggerWithNoListener()
    {
        $event = new Event('test.event1');
        $this->assertFalse($event->trigger());
    }

    public function testClearEvent()
    {
        $testObject = $this->getMockBuilder('stdClass')->setMethods(['testCallback1'])->getMock();
        $testObject->expects($this->never())
            ->method('testCallback1');
        EventManager::register('test.event1', array($testObject, 'testCallback1'));
        EventManager::clear('test.event1');
        $event = new Event('test.event1');
        $event->trigger();
    }

    public function testStopPropagationVarient1()
    {
        $testObject = $this->getMockBuilder('stdClass')->setMethods(['testCallback1'])->getMock();
        $testObject->expects($this->never())
            ->method('testCallback1');
        $event = new Event('test.event1');
        EventManager::register('test.event1', function (Event $event) {
            return false;
        });
        EventManager::register('test.event1', array($testObject, 'testCallback1'));
        $this->assertTrue($event->trigger());
    }

    public function testStopPropagationVarient2()
    {
        $testObject = $this->getMockBuilder('stdClass')->setMethods(['testCallback1'])->getMock();
        $testObject->expects($this->never())
            ->method('testCallback1');
        $event = new Event('test.event1');
        EventManager::register('test.event1', function (Event $event) {
            $event->stopPropagation();
        });
        EventManager::register('test.event1', array($testObject, 'testCallback1'));
        $this->assertTrue($event->trigger());
    }

    public function testEventName()
    {
        $testObject = $this->getMockBuilder('stdClass')->setMethods(['testCallback1'])->getMock();
        $testObject->expects($this->once())
            ->method('testCallback1');
        EventManager::register('test.event1', array($testObject, 'testCallback1'));
        $event = new Event('test.event1');
        $event->trigger();
        $event->name('test.event2');
        $event->trigger();
    }

    public function testSuggestingPreventAction()
    {
        $event = new Event('test.event1');
        EventManager::register('test.event1', function (Event $event) {
            $event->preventAction();
        });
        $event->trigger();
        $this->assertTrue($event->shouldPreventAction());
    }

    public function testSuggestingPreventActionWithMessage()
    {
        $event = new Event('test.event1');
        EventManager::register('test.event1', function (Event $event) {
            $event->preventAction(true, 'Reason why should prevent.');
        });
        $event->trigger();
        $this->assertTrue($event->shouldPreventAction());
        $this->assertEquals('Reason why should prevent.', $event->preventActionMessage());
    }

    public function testEventPriority()
    {
        // lower priority
        $lowPriority = function (Event $event) {
            EventManagerTest::$order[] = 3;
        };
        $highPriority = function (Event $event) {
            EventManagerTest::$order[] = 1;
        };
        $alsoHighPriority = function (Event $event) {
            EventManagerTest::$order[] = 2;
        };
        EventManager::register('test.event1', $lowPriority, EventRegistry::PRIORITY_LOW);
        EventManager::register('test.event1', $highPriority, EventRegistry::PRIORITY_HIGH);
        EventManager::register('test.event1', $alsoHighPriority, EventRegistry::PRIORITY_HIGH);
        $event = new Event('test.event1');
        $event->trigger();
        $this->assertEquals(array(1, 2, 3), static::$order);
    }
}
