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
use \Zumba\Symbiosis\Event\Event;
use \Zumba\Symbiosis\Plugin\PluginManager;
use \Zumba\Symbiosis\Framework\Plugin;
use \Zumba\Symbiosis\Framework\Registerable;

/**
 * @group event
 */
class EventTest extends TestCase
{
    public function tearDown() : void
    {
        parent::tearDown();
        EventManager::clearAll();
    }

    public function testOverrideData()
    {
        $data = array(
            'package' => true
        );
        $event = new Event('test', $data);
        $this->assertEquals(array('package' => true), $event->data());
        $event->data(array('package2' => true));
        $this->assertEquals(array('package2' => true), $event->data());
    }

    public function testAppendData()
    {
        $data = array(
            'package' => true
        );
        $event = new Event('test', $data);
        $this->assertEquals(array('package' => true), $event->data());
        $data2 = array(
            'package2' => true
        );
        $event->data($data2, true);
        $this->assertEquals(array('package' => true, 'package2' => true), $event->data());
    }

    public function testPluginContext()
    {
        $manager = new PluginManager('', '');
        $testPlugin = $this->getMockBuilder('Zumba\Symbiosis\Test\Plugin\MockablePlugin')->setMethods(['mockMe'])->getMock();
        $testPlugin
            ->expects($this->once())
            ->method('mockMe');
        $manager->initializePlugin($testPlugin);
        $event = new Event('test', ['val' => true]);
        $event
            ->setPluginContext($manager)
            ->trigger();
    }
}
