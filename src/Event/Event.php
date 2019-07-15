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
namespace Zumba\Symbiosis\Event;

use \Zumba\Symbiosis\Event\EventRegistry;
use \Zumba\Symbiosis\Event\EventManager;
use \Zumba\Symbiosis\Plugin\PluginManager;
use \Zumba\Symbiosis\Framework\EventInterface;
use \Psr\EventDispatcher\StoppableEventInterface;

class Event implements EventInterface
{

    /**
     * Name of event.
     *
     * @var string
     */
    protected $name;

    /**
     * Data to pass to event listeners.
     *
     * @var array
     */
    protected $data;

    /**
     * Is this event currently being processed by listeners?
     *
     * @var boolean
     */
    protected $isPropagating = true;

    /**
     * Suggestion to application and other plugins if further action should be halted.
     *
     * @var boolean
     */
    protected $shouldPreventAction = false;

    /**
     * Reason to suggest to the application to stop further action.
     *
     * @var string
     */
    protected $preventActionMessage = '';

    /**
     * Holds reference to plugin manager that spawned this event.
     *
     * @var \Zumba\Symbiosis\Plugin\PluginManager
     */
    protected $pluginContext;

    /**
     * Constructor.
     *
     * @param string $name
     * @param array $data
     */
    public function __construct($name, $data = array())
    {
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * Get/set the name of this event.
     *
     * @param string $name
     * @return string
     */
    public function name(?string $name = null) : string
    {
        if ($name !== null) {
            $this->name = $name;
        }
        return $this->name;
    }

    /**
     * Get/set the data of this event.
     *
     * Append option is used to append passed data instead of over-writing.
     *
     * @param array $data
     * @param boolean $append Default no
     * @return array
     */
    public function data(?array $data = null, bool $append = false) : array
    {
        if ($data !== null) {
            if ($append) {
                $this->data = array_merge($this->data, (array)$data);
            } else {
                $this->data = $data;
            }
        }
        return $this->data;
    }

    /**
     * Is this event propagating?
     *
     * @return boolean
     */
    public function isPropagating()
    {
        return $this->isPropagating;
    }

    /**
     * Is propagation stopped?
     *
     * This will typically only be used by the Dispatcher to determine if the
     * previous listener halted propagation.
     *
     * @return bool
     *   True if the Event is complete and no further listeners should be called.
     *   False to continue calling listeners.
     */
    public function isPropagationStopped() : bool
    {
        return !$this->isPropagating();
    }

    /**
     * Set the plugin manager context for this event.
     *
     * @param PluginManager $manager
     * @return \Zumba\Symbiosis\Event\Event
     */
    public function setPluginContext(PluginManager $manager)
    {
        $this->pluginContext = $manager;
        return $this;
    }

    /**
     * Stops this event from propagating further.
     *
     * @return void
     */
    public function stopPropagation() : void
    {
        $this->isPropagating = false;
    }

    /**
     * Determines if it is suggested to prevent further actions.
     *
     * @return boolean
     */
    public function shouldPreventAction()
    {
        return $this->shouldPreventAction;
    }

    /**
     * Gets the reason to prevent further actions.
     *
     * @return string
     */
    public function preventActionMessage()
    {
        return $this->preventActionMessage;
    }

    /**
     * Sets the suggestion of preventing further action.  Default true.
     *
     * @param boolean $prevent
     * @param string $message
     * @return void
     */
    public function preventAction($prevent = true, $message = '')
    {
        $this->shouldPreventAction = (boolean)$prevent;
        $this->preventActionMessage = (string)$message;
    }

    /**
     * Convenience method for triggering the event from the event object.
     *
     * @param  Zumba\Symbiosis\EventRegistry $registry Single registry instance if used outside of global context.
     * @return boolean
     */
    public function trigger(EventRegistry $registry = null)
    {
        if ($this->pluginContext) {
            return $this->pluginContext->trigger($this);
        }
        return $registry instanceof EventRegistry ?
            $registry->trigger($this) :
            EventManager::trigger($this);
    }
}
