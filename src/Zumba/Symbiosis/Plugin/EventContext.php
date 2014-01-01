<?php

namespace Zumba\Symbiosis\Plugin;

use \Zumba\Symbiosis\Event\EventRegistry;

class EventContext {

	/**
	 * Registry holder.
	 * 
	 * @var Zumba\Symbiosis\Event\EventRegistry
	 */
	protected $registry;

	/**
	 * Constructor.
	 * 
	 * @param Zumba\Symbiosis\Event\EventRegistry $pluginRegistry
	 */
	public function __construct(EventRegistry $pluginRegistry) {
		$this->registry = $pluginRegistry;
	}

	/**
	 * Get the event registry for this context.
	 *
	 * @return Zumba\Symbiosis\Plugin\EventContext
	 */
	public function registry() {
		return $this->registry;
	}

}
