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
namespace Zumba\Symbiosis\Framework;

use \Zumba\Symbiosis\Event\EventRegistry;

abstract class Plugin
{
    const PRIORITY_DEFAULT = 100;

    /**
     * Determines the order in which the plugins are executed for a specific event.
     *
     * @var integer
     */
    public $priority = self::PRIORITY_DEFAULT;

    /**
     * Determines if this plugin should be included as activated.
     *
     * @var boolean
     */
    public $enabled = true;

    /**
     * Plugin context for this plugin's instance.
     *
     * @var \Zumba\Symbiosis\Event\EventRegistry
     */
    protected $context;

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Get/set the event context for this plugin.
     *
     * @param \Zumba\Symbiosis\Event\EventRegistry $context
     * @return \Zumba\Symbiosis\Event\EventRegistry
     */
    public function eventContext(EventRegistry $context = null)
    {
        if ($context !== null) {
            $this->context = $context;
        }
        return $this->context;
    }

    /**
     * Name this plugin.
     *
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }

    /**
     * Return a hash array containing the event name as the key and a callback or array of callbacks as the value.
     *
     * @return array
     */
    abstract public function getEvents();

    /**
     * Binds events specified in the events property.
     *
     * @return void
     * @throws \Zumba\Symbiosis\Exception\NotCallableException
     */
    public function bindPluginEvents()
    {
        foreach ($this->getEvents() as $key => $callbacks) {
            if (is_array($callbacks) && (!isset($callbacks[0]) || !is_array($callbacks[0]))) {
                $callbacks = [$callbacks];
            }
            foreach ((array)$callbacks as $callback) {
                $this->context->register($key, $callback);
            }
        }
    }
}
