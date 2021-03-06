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
namespace Zumba\Symbiosis\Test\Plugin\SamplePlugins;

use \Zumba\Symbiosis\Framework\Plugin;

class TestCPlugin extends Plugin
{
    public $priority = 2; // Same priority as TestAPlugin

    public function registerEvents()
    {
        // Do nothing
    }

    public function getEvents()
    {
        return [];
    }
}
