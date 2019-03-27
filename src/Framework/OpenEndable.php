<?php

namespace Zumba\Symbiosis\Framework;

interface OpenEndable
{

    /**
     * Main method called when plugin is initialized to register events to listen.
     *
     * @return void
     */
    public function registerEvents();
}
