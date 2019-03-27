<?php

namespace Zumba\Symbiosis\Test\Plugin;

use \Zumba\Symbiosis\Framework\Plugin;
use \Zumba\Symbiosis\Framework\Registerable;

class MockablePlugin extends Plugin implements Registerable
{
    public function getEvents()
    {
        return [
            'test' => [$this, 'mockMe']
        ];
    }

    public function mockMe()
    {
    }
}
